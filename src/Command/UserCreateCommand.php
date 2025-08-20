<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'user:create',
    description: 'Crea un usuario solicitando mail, nombre y contraseña.'
)]
class UserCreateCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserRepository $users,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly ValidatorInterface $validator,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Crear usuario');

        $helper = $this->getHelper('question');

        // Ask for email
        $qEmail = new Question('Email: ');
        $qEmail->setValidator(function ($answer) {
            $answer = is_string($answer) ? trim($answer) : '';
            $violations = $this->validator->validate($answer, [
                new Assert\NotBlank(message: 'El email es requerido.'),
                new Assert\Email(message: 'El email no es válido.'),
            ]);
            if ($violations->count() > 0) {
                throw new \RuntimeException($violations[0]->getMessage());
            }
            return strtolower($answer);
        });
        $email = $helper->ask($input, $output, $qEmail);

        // Check uniqueness
        if ($this->users->findOneBy(['email' => $email])) {
            $io->error(sprintf('Ya existe un usuario con el email "%s".', $email));
            return Command::FAILURE;
        }

        // Ask for name
        $qName = new Question('Nombre: ');
        $qName->setValidator(function ($answer) {
            $answer = is_string($answer) ? trim($answer) : '';
            $violations = $this->validator->validate($answer, [
                new Assert\NotBlank(message: 'El nombre es requerido.'),
                new Assert\Length(max: 255, maxMessage: 'El nombre no puede superar 255 caracteres.'),
            ]);
            if ($violations->count() > 0) {
                throw new \RuntimeException($violations[0]->getMessage());
            }
            return $answer;
        });
        $name = $helper->ask($input, $output, $qName);

        // Ask for password (hidden)
        $qPassword = new Question('Contraseña: ');
        $qPassword->setHidden(true);
        $qPassword->setHiddenFallback(false);
        $qPassword->setValidator(function ($answer) {
            $answer = is_string($answer) ? $answer : '';
            $violations = $this->validator->validate($answer, [
                new Assert\NotBlank(message: 'La contraseña es requerida.'),
                new Assert\Length(min: 6, minMessage: 'La contraseña debe tener al menos {{ limit }} caracteres.'),
            ]);
            if ($violations->count() > 0) {
                throw new \RuntimeException($violations[0]->getMessage());
            }
            return $answer;
        });
        $password = $helper->ask($input, $output, $qPassword);

        // Progress bar for steps
        $io->section('Creando usuario...');
        $steps = 4; // construir entidad, hashear password, persistir, confirmar guardado
        $progressBar = new ProgressBar($output, $steps);
        $progressBar->start();

        try {
            // 1. Build entity
            $user = new User();
            $user->setEmail($email);
            $user->setName($name);
            $progressBar->advance();

            // 2. Hash password
            $hashed = $this->hasher->hashPassword($user, $password);
            $user->setPassword($hashed);
            $progressBar->advance();

            // 3. Persist
            $this->em->persist($user);
            $this->em->flush();
            $progressBar->advance();

            // 4. Verify
            $saved = $this->users->findOneBy(['email' => $email]);
            if (!$saved) {
                throw new \RuntimeException('No se pudo verificar la creación del usuario.');
            }
            $progressBar->advance();

            $progressBar->finish();
            $output->writeln('');
            $io->success(sprintf('Usuario creado con éxito. ID: %s, Email: %s, Nombre: %s', $saved->getId(), $saved->getEmail(), $saved->getName()));
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $progressBar->finish();
            $output->writeln('');
            $io->error('Error al crear el usuario: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

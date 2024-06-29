<?php

namespace App\DTO;

use App\Entity\Personales;

class PersonalesDataTransformer
{

    public ?string $nombre = null;
    public ?string $apellido = null;
    public ?string $email = null;
    public ?string $observaciones = null;
    public ?string $phone = null;
    public ?string $point = null;

    public function __construct(array $requestData)
    {
        $this->nombre = $requestData['nombre'];
        $this->apellido = $requestData['apellido'];
        $this->email = $requestData['email'];
        $this->phone = $requestData['phone'];
        $this->point = $requestData['point'];
        $this->observaciones = $requestData['observaciones']??'';
    }

    public function transform(): Personales
    {
        $personal = new Personales();

        $personal->setNombre($this->nombre);
        $personal->setApellido($this->apellido);
        $personal->setEmail($this->email);
        $personal->setPhone($this->phone);
        $personal->setPoint($this->point);
        $personal->setObservaciones($this->observaciones);

        return $personal;


    }




}
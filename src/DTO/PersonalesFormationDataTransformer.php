<?php

namespace App\DTO;

use App\Entity\Dones;
use App\Entity\Personales;
use App\Entity\PersonalFormation;
use Doctrine\Persistence\ManagerRegistry;

class PersonalesFormationDataTransformer
{
    public ?int $percentDon = null;
    public ?string $commentDon = null;
    public $don = null; // could be id, iri, identifier
    public $person = null; // could be id, iri

    private ManagerRegistry $registry;

    public function __construct(array $requestData, ?ManagerRegistry $registry = null)
    {
        $this->percentDon = isset($requestData['percentDon']) ? (int) $requestData['percentDon'] : null;
        $this->commentDon = $requestData['commentDon'] ?? null;
        $this->don = $requestData['don'] ?? null;
        $this->person = $requestData['person'] ?? null;
        // Optional injection via controller; if null, we cannot resolve relations
        if ($registry) {
            $this->registry = $registry;
        }
    }

    public function transform(): PersonalFormation
    {
        $personalFormation = new PersonalFormation();
        if ($this->percentDon !== null) {
            $personalFormation->setPercentDon($this->percentDon);
        }
        if ($this->commentDon !== null) {
            $personalFormation->setCommentDon($this->commentDon);
        } else {
            // DB column is NOT NULL; default to empty string when comment absent
            $personalFormation->setCommentDon('');
        }

        // Resolve relations if registry available
        if (isset($this->registry)) {
            $em = $this->registry->getManager();

            // Resolve Person (support UUID or numeric IDs and IRI forms)
            $personEntity = null;
            $personRepo = $em->getRepository(Personales::class);

            if (is_string($this->person)) {
                $p = trim($this->person);
                if ($p !== '') {
                    if (str_starts_with($p, '/')) {
                        // IRI like /api/personal/{id-or-uuid}
                        $tail = substr($p, strrpos($p, '/') + 1);
                        $tail = trim($tail);
                        if ($tail !== '') {
                            // Try numeric first, then as string (UUID)
                            if (ctype_digit($tail)) {
                                $personEntity = $personRepo->find((int)$tail);
                            }
                            if (!$personEntity) {
                                $personEntity = $personRepo->find($tail);
                            }
                        }
                    } else {
                        // Raw id: could be UUID or numeric string
                        if (ctype_digit($p)) {
                            $personEntity = $personRepo->find((int)$p);
                        }
                        if (!$personEntity) {
                            $personEntity = $personRepo->find($p);
                        }
                    }
                }
            } elseif (is_numeric($this->person)) {
                $personEntity = $personRepo->find((int)$this->person);
            } elseif (is_array($this->person)) {
                if (isset($this->person['id'])) {
                    $pid = $this->person['id'];
                    if (is_string($pid)) {
                        $pid = trim($pid);
                        if ($pid !== '') {
                            if (ctype_digit($pid)) {
                                $personEntity = $personRepo->find((int)$pid);
                            }
                            if (!$personEntity) {
                                $personEntity = $personRepo->find($pid);
                            }
                        }
                    } elseif (is_numeric($pid)) {
                        $personEntity = $personRepo->find((int)$pid);
                    }
                } elseif (isset($this->person['@id']) && is_string($this->person['@id'])) {
                    $iri = trim($this->person['@id']);
                    if ($iri !== '') {
                        $tail = substr($iri, strrpos($iri, '/') + 1);
                        $tail = trim($tail);
                        if ($tail !== '') {
                            if (ctype_digit($tail)) {
                                $personEntity = $personRepo->find((int)$tail);
                            }
                            if (!$personEntity) {
                                $personEntity = $personRepo->find($tail);
                            }
                        }
                    }
                }
            }

            if ($personEntity) {
                $personalFormation->setPerson($personEntity);
            }

            // Resolve Don
            $donEntity = null;
            if (is_string($this->don) && str_starts_with($this->don, '/')) {
                $id = (int) substr($this->don, strrpos($this->don, '/') + 1);
                if ($id) $donEntity = $em->getRepository(Dones::class)->find($id);
            } elseif (is_numeric($this->don)) {
                $donEntity = $em->getRepository(Dones::class)->find((int)$this->don);
            } elseif (is_string($this->don)) {
                // maybe identifier
                $donEntity = $em->getRepository(Dones::class)->findOneBy(['identifier' => $this->don]);
            }
            if ($donEntity) {
                $personalFormation->setDon($donEntity);
            }
        }

        return $personalFormation;
    }
}
<?php
namespace toubilib\application\dtos;

use toubilib\application\exceptions\ValidationException;
use Respect\Validation\Validator as v;
class CreateRdvDTO
{
    private array $data;
    private function __construct(array $data)
    {
        $this->data = $data;
    }
    public static function fromArray(array $data): self
    {
        // 1) Validation syntaxique : présence, structure, type
        v::key('idPatient', v::intType()->notEmpty())
            ->key('idMedecin', v::intType()->notEmpty())
            ->key('Motif', v::stringType()->notEmpty())
            ->key('DateHeure', v::dateTime('d-m-Y H:i:s')->min('now'))
            ->assert($data);
        // 2) Sanitization : on nettoie les champs texte
        if (isset($data['Motif'])) {
            $data['Motif'] = filter_var($data['Motif'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }

        // Le DTO final contient les données validées ET nettoyées
        return new self($data);
    }
    public function toArray(): array
    {
        return $this->data;
    }
}
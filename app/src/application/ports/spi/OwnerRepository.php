<?php
namespace toubilib\application\ports\spi;

use toubilib\domain\entities\Owner;

interface OwnerRepository {

    // ── @return Owner
    public function findById(string $id): ?Owner;

}
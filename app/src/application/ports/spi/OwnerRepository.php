<?php
namespace toubilib\application\ports\spi;

use jira\domain\entities\Owner;

interface OwnerRepository {

    // ── @return Owner
    public function findById(string $id): ?Owner;

}
<?php
namespace HenryAvila\EmailTracking\Contracts;

use BackedEnum;

interface HasEmailType
{
    /**
     * Define o tipo de email para categorização.
     *
     * @return string|BackedEnum Tipo do email (string ou enum)
     */
    public function getEmailType(): string|BackedEnum;
}

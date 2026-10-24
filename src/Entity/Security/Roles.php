<?php

declare(strict_types=1);

namespace App\Entity\Security;

final class Roles
{
    public const string USER = 'ROLE_USER';

    public const string CREATE_APPOINTMENT = 'ROLE_CREATE_APPOINTMENT';
    public const string READ_APPOINTMENT = 'ROLE_READ_APPOINTMENT';
    public const string UPDATE_APPOINTMENT = 'ROLE_UPDATE_APPOINTMENT';
    public const string DELETE_APPOINTMENT = 'ROLE_DELETE_APPOINTMENT';

    public const string CREATE_GUARDIAN = 'ROLE_CREATE_GUARDIAN';
    public const string READ_GUARDIAN = 'ROLE_READ_GUARDIAN';
    public const string UPDATE_GUARDIAN = 'ROLE_UPDATE_GUARDIAN';
    public const string DELETE_GUARDIAN = 'ROLE_DELETE_GUARDIAN';

    public const string CREATE_KID = 'ROLE_CREATE_KID';
    public const string READ_KID = 'ROLE_READ_KID';
    public const string UPDATE_KID = 'ROLE_UPDATE_KID';
    public const string DELETE_KID = 'ROLE_DELETE_KID';

    public const string CREATE_KID_GROUP = 'ROLE_CREATE_KID_GROUP';
    public const string READ_KID_GROUP = 'ROLE_READ_KID_GROUP';
    public const string UPDATE_KID_GROUP = 'ROLE_UPDATE_KID_GROUP';
    public const string DELETE_KID_GROUP = 'ROLE_DELETE_KID_GROUP';

    public const string CREATE_USER = 'ROLE_CREATE_USER';
    public const string READ_USER = 'ROLE_READ_USER';
    public const string UPDATE_USER = 'ROLE_UPDATE_USER';
    public const string DELETE_USER = 'ROLE_DELETE_USER';

    public static function cases(): array
    {
        return [
            self::CREATE_APPOINTMENT => self::CREATE_APPOINTMENT,
            self::READ_APPOINTMENT => self::READ_APPOINTMENT,
            self::UPDATE_APPOINTMENT => self::UPDATE_APPOINTMENT,
            self::DELETE_APPOINTMENT => self::DELETE_APPOINTMENT,
            self::CREATE_GUARDIAN => self::CREATE_GUARDIAN,
            self::READ_GUARDIAN => self::READ_GUARDIAN,
            self::UPDATE_GUARDIAN => self::UPDATE_GUARDIAN,
            self::DELETE_GUARDIAN => self::DELETE_GUARDIAN,
            self::CREATE_KID => self::CREATE_KID,
            self::READ_KID => self::READ_KID,
            self::UPDATE_KID => self::UPDATE_KID,
            self::DELETE_KID => self::DELETE_KID,
            self::CREATE_KID_GROUP => self::CREATE_KID_GROUP,
            self::READ_KID_GROUP => self::READ_KID_GROUP,
            self::UPDATE_KID_GROUP => self::UPDATE_KID_GROUP,
            self::DELETE_KID_GROUP => self::DELETE_KID_GROUP,
            self::CREATE_USER => self::CREATE_USER,
            self::READ_USER => self::READ_USER,
            self::UPDATE_USER => self::UPDATE_USER,
            self::DELETE_USER => self::DELETE_USER,
        ];
    }
}

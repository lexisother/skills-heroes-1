<?php

namespace App\DTO;

use InvalidArgumentException;

class Teacher
{
    public int $id;
    public string $first_name;
    public string $last_name;
    public int $age;
    public string $class;
    public string $email;
    public string $phone;
    public string $work_days;

    public function __construct($id, $first_name, $last_name, $age, $class, $email, $phone, $work_days)
    {
        if (is_int($id)) {
            $this->id = $id;
        } else {
            throw new InvalidArgumentException('$id must be an integer');
        }

        if (is_string($first_name)) {
            $this->first_name = $first_name;
        } else {
            throw new InvalidArgumentException('$first_name must be a string');
        }

        if (is_string($last_name)) {
            $this->last_name = $last_name;
        } else {
            throw new InvalidArgumentException('$last_name must be a string');
        }

        if (is_int($age)) {
            $this->age = $age;
        } else {
            throw new InvalidArgumentException('$age must be a integer');
        }

        if (is_string($class)) {
            $this->class = $class;
        } else {
            throw new InvalidArgumentException('$class must be a string');
        }

        if (is_string($email)) {
            $this->email = $email;
        } else {
            throw new InvalidArgumentException('$email must be a string');
        }

        if (is_string($phone)) {
            $this->phone = $phone;
        } else {
            throw new InvalidArgumentException('$phone must be a string');
        }

        if (is_string($work_days)) {
            $this->work_days = $work_days;
        } else {
            throw new InvalidArgumentException('$work_days must be a string');
        }
    }
}

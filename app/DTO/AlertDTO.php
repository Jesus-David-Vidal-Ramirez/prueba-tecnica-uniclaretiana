<?php

namespace App\DTO;

use App\Enums\AlertType;
use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;

class AlertDTO implements Arrayable
{
    public AlertType $type;
    public string $title;
    public string $message;

    private const VALID_TYPES = ['success', 'error', 'warning', 'info'];

    public function __construct(AlertType $type, string $title, string $message)
    {
        // if (! in_array($type, self::VALID_TYPES)) {
        //     throw new InvalidArgumentException("Tipo de alerta inválido");
        // }

        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
    }

    public static function success(string $title, string $message): self
    {
        return new self(AlertType::SUCCESS, $title, $message);
    }

    public static function error(string $title, string $message): self
    {
        return new self(AlertType::ERROR, $title, $message);
    }

    public static function info(string $title, string $message): self
    {
        return new self(AlertType::INFO, $title, $message);
    }

    public static function warning(string $title, string $message): self
    {
        return new self(AlertType::WARNING, $title, $message);
    }

    public function toArray(): array
    {
        return [
            'type'    => $this->type->value,
            'title'   => $this->title,
            'message' => $this->message,
        ];
    }
}

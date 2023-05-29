<?php

namespace App\libs;

final class Response
{
    public static $data = [];

    public function addTo($token, $value, $options = [])
    {
        if (array_search($token, array("errorGameMessage", "gameMessage", "data", "levelUP", "html")) === false) {
            return false;
        }
        switch ($token) {
            case 'gameMessage':
                self::$data['gameMessages'][] = $value;
                $this->gameMessage($value, true);
                break;
            case 'errorGameMessage':
                $message = "ERROR " . $value;
                self::$data['gameMessages'][] = $message;
                $this->gameMessage($message, true);
                break;
            case 'data':
                self::$data[$options['index']] = $value;
                break;
            case 'levelUP':
                self::$data['levelUP'] = $value;
                break;
            case 'html':
                self::$data['html'][] = $value;
                break;
            default:

                break;
        }
    }
    public function send()
    {
        if (isset(self::$data['html']) && count(self::$data['html']) === 1) {
            self::$data['html'] = self::$data['html'][0];
        }
        return json_encode(self::$data);
    }


    public function gameMessage($message, $ajax = false)
    {
        $date = '[' . date("H:i:s") . '] ';
        $messageString = $date . trim($message);
        $_SESSION['log'][] = $messageString;
        if (count($_SESSION['log']) > 15) {
            unset($_SESSION['log'][0]);
            $_SESSION['log'] = array_values($_SESSION['log']);
        }
        if ($ajax === false) {
            return $messageString;
        }
    }


    public static function addMessage(string $message)
    {
        static::$data['message'][] = $message;
        return new static();
    }

    public static function addErrorMessage(string $message)
    {
        static::$data['message'][] = $message;
        return new static;
    }

    public static function addLevelUP(string $skillName, int $new_level)
    {
        self::$data['levelUP'][] = [
            'skill' => $skillName,
            'new_level' => $new_level
        ];
        return new self();
    }

    public static function addData(string $index, $data)
    {
        self::$data['data'][$index] = $data;
        return new self();
    }

    public static function setData($data)
    {
        self::$data['data'] = $data;
        return new self;
    }

    public static function setResponse($data)
    {
        self::$data = $data;
    }

    public static function addTemplate(string $index, $content)
    {
        self::$data['html'][$index] = $content;
        return new self;
    }

    public static function getData()
    {
        return self::$data;
    }

    public static function get()
    {
        return json_encode(self::$data);
    }

    public static function setStatus(int $status)
    {
        http_response_code($status);
        return new static;
    }

    public static function toJson()
    {
        return json_encode(self::$data);
    }

    public static function clear()
    {
        self::$data = [];
    }
}

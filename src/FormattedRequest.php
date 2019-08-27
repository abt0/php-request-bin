<?php

declare(strict_types=1);

final class FormattedRequest
{
    private const CLR = "\e[m";
    private const NL = "\n";
    private const DNL = "\n\n";
    private const BG_RED = "\e[48;5;202m";
    private const FG_RED = "\e[38;5;202m";
    private const FG_WHITE = "\e[97m";

    private $output;

    private function addRedTitle(string $title): void
    {
        $this->output .= self::NL . self::FG_RED . '-> ' . $title . ': ' . self::CLR . self::DNL;
    }

    private function addNameValue(string $name, string $value): void
    {
        $this->output .= self::FG_WHITE . $name . self::CLR . ': ' . $value . self::NL;
    }

    private function addList(array $array, string $title): void
    {
        $this->addRedTitle($title);

        foreach ($array as $name => $value) {
            $this->addNameValue((string)$name, (string)$value);
        }
    }

    private function addBody(string $contentType): void
    {
        $body = file_get_contents('php://input');

        if ($body) {
            $this->output .= self::NL . self::FG_RED . '-> ';

            $this->output .= 'RAW BODY: ' . self::CLR . self::DNL;

            $this->output .= $body . self::NL;

            if ($contentType === 'application/json') {

                $body = json_encode(
                    json_decode($body, true),
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                );

                $this->output .= 'JSON BODY: ' . self::CLR . self::DNL;

                $this->output .= $body . self::NL;
            }
        }
    }

    public function __construct()
    {
        $this->output = self::NL . self::BG_RED . self::FG_WHITE . ' Incoming request ' . self::CLR . self::NL;

        $this->addRedTitle('HEADERS');

        $contentType = '';

        foreach (getallheaders() as $name => $value) {
            if ($name === 'Content-Type') {
                $contentType = $value;
            }

            $this->addNameValue((string)$name, (string)$value);
        }

        $_SERVER and $this->addList($_SERVER, 'SERVER');
        $_GET and $this->addList($_GET, 'GET');
        $_POST and $this->addList($_POST, 'POST');
        $_COOKIE and $this->addList($_COOKIE, 'COOKIE');
        $_FILES and $this->addList($_FILES, 'FILES');
        $_ENV and $this->addList($_ENV, 'ENV');

        $this->addBody($contentType);

        $this->output .= self::NL . self::FG_RED . '///' . self::CLR . self::NL;
    }

    public function output(): string
    {
        return $this->output;
    }
}

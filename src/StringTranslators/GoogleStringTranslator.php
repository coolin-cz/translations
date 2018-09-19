<?php

declare(strict_types=1);

namespace Freelo\Translations\StringTranslators;

use Freelo\Translations\Exceptions\NoTranslatedMessageFound;
use Freelo\Translations\Repositories\ITranslationsRepository;
use Nette\Utils\Json;

final class GoogleStringTranslator implements IStringTranslator
{

    /** @var array */
    private $defaults = [
        'host' => 'https://translate.googleapis.com/translate_a/single?client=gtx&sl=%s&tl=%s&dt=t&q=%s',
        'headers' => ['Accept: application/json'],
        'userAgent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13',
        'searchPhrases' => ['% s', '% d', '% d', '</ ', '& nbsp;', '" %s"', '& ndash;', '% S', '...'],
        'replacePhrases' => [' %s', ' %d', ' %d', '</', '&nbsp;', '„%s“', '&ndash;', '%s', '…'],
    ];

    /** @var ITranslationsRepository */
    private $translationsRepository;


    public function __construct(ITranslationsRepository $translationsRepository)
    {
        $this->translationsRepository = $translationsRepository;
    }


    /**
     * @throws \RuntimeException
     */
    public function translate(string $message, string $fromLanguage, string $toLanguage): string
    {
        // check if message is already translated
        try {
            return $this->translationsRepository->getTranslatedMessage($message, $fromLanguage, $toLanguage);

        } catch (NoTranslatedMessageFound $e) {

        }

        // translate message
        $url = sprintf($this->defaults['host'], $fromLanguage, $toLanguage, urlencode($message));

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->defaults['headers']);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_USERAGENT, $this->defaults['userAgent']);

        $data = curl_exec($ch);

        if ($data === false) {
            throw new \RuntimeException(curl_error($ch));
        }

        curl_close($ch);

        $json = Json::decode($data, Json::FORCE_ARRAY);

        $translated = [];
        if (isset($json[0]) && \is_array($json[0])) {
            foreach ($json[0] as $part) {
                $translated[] = trim($part[0]);
            }
        }
        $translated = implode(' ', $translated);

        return str_replace($this->defaults['searchPhrases'], $this->defaults['replacePhrases'], $translated);
    }

}

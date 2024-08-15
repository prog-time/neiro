<?php

if (!function_exists('separateText')) {
    /**
     * Разделение текста на абзатцы или предложения
     *
     * @param string $text - текст для разделения
     * @param string $separator - правила разделения
     * @return array
     */
    function separateText(string $text, string $separator = "proposal"): array
    {
        $resultData = [];
        switch ($separator) {
            case "proposal": // по предложениям
                $text = preg_replace('/\s\s+/', ' ', $text);
                $resultData = preg_split('/(?<=[.?!])\s+(?=[a-zа-яё])/i', $text);
                break;

            case "paragraph": // по абзацам
                $text = preg_replace('/\n\n+/', " \n ", $text);
                $resultData = preg_split('/\n+/', $text);
                break;
        }
        return $resultData;
    }
}



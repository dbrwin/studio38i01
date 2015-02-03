<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Processor;

use Sunra\PhpSimple\HtmlDomParser;
use simple_html_dom;

class Parser
{
    protected $httpClient;

    /**
     * @return Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @param mixed $httpClient
     */
    public function setHttpClient($httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param $htmlStr
     * @return \simple_html_dom
     */
    public function getDom($htmlStr)
    {
        if (extension_loaded("mbstring") && ini_get("mbstring.func_overload") != 0) {
            throw new \Exception("mbstring.func_overload must be set to 0");
        }
        return HtmlDomParser::str_get_html($htmlStr);
    }

    public function parseSolutionsLinks($htmlStr)
    {
        $htmlStr = $this->prepareHtml($htmlStr);

        if (extension_loaded("tidy")) {

            // Установка конфигурации
            $config = array(
                'indent'       => true,
                'output-xhtml' => true,
                'wrap'         => 200);
// Tidy
            $tidy = new \tidy;
            $tidy->parseString($htmlStr, $config, 'raw');
            $tidy->cleanRepair();
            $htmlStr = $tidy->body();
        }

        $dom = $this->getDom($htmlStr);
        if (!$dom instanceof \simple_html_dom) {
            throw new \Exception("Error in get dom from string: " . htmlspecialchars($htmlStr));
        }

        $tables = $dom->find("table.content");
        if (empty($tables)) {
            throw new \Exception("Not fount table.content in dom");
        }
        $table = count($tables) > 1 ? $tables[1] : $tables[0];

        $tableRows = $table->find("tr");
        array_shift($tableRows);

        $links = [];
        foreach ($tableRows as $row) {
            $link = $row->children(1)->find("a", 0)->href;
            $link = explode("=", $link);
            $links[] = $link[1];
        }
        return $links;
    }

    public function prepareHtml($text)
    {
        return preg_replace('/^\s+|\n|\r|\s+| +$/s', ' ', $text);
    }

    public function parseSolutionsListPagination($text)
    {
        if (preg_match('~parts=(\d+?)[\'\"]{1}>&nbsp;&raquo;&raquo;<\/a>~', $text, $matches)) {
            return (integer)$matches[1];
        }
        return null;
    }

    public function getTypeSolution($text)
    {
        if (preg_match('~Внедренное типовое решение:<\/b><\/td> <td>(.+?)<\/td>~', $text, $matches)) {
            return trim($matches[1]);
        }
        return false;
    }

    public function calcIndustry(array $inData)
    {
        $tplArray = [
            [
                "tpl"  => ["Здравоохранение"],
                "item" => "Здравоохранение"
            ],
            [
                "tpl"  => ["Образование, культура, наука"],
                "item" => "Образование и культура"
            ],
            [
                "tpl"  => ["Общественное и плановое питание, гостиничный бизнес, туризм"],
                "item" => "Хорека и туризм"
            ],
            [
                "tpl"  => ["Производство, ТЭК"],
                "item" => "Производство"
            ],
            [
                "tpl"  => ["Профессиональные услуги"],
                "item" => "Услуги"
            ],
            [
                "tpl"  => ["Сельское и лесное хозяйство"],
                "item" => "Сельское хозяйство"
            ],
            [
                "tpl"  => ["Строительство, девелопмент, ЖКХ"],
                "item" => "Строительство и ЖКХ"
            ],
            [
                "tpl"  => ["Торговля, склад, логистика, транспорт", "Торговля"],
                "item" => "Торговля"
            ],
            [
                "tpl"  => ["Торговля, склад, логистика, транспорт", "Транспорт"],
                "item" => "Логистика и транспорт"
            ],
            [
                "tpl"  => ["Торговля, склад, логистика, транспорт", "Логистика и управление складским хозяйством"],
                "item" => "Логистика и транспорт"
            ],
            [
                "tpl"  => ["Торговля, склад, логистика, транспорт", "Другие предприятия торговли, складского хозяйства и транспорта"],
                "item" => "Логистика и транспорт"
            ],
            [
                "tpl"  => ["Финансовый сектор"],
                "item" => "Услуги"
            ],
            [
                "tpl"  => ["Другие предприятия и организации"],
                "item" => "Услуги"
            ], [
                "tpl"  => ["Государственное и муниципальное управление, силовые структуры, другие бюджетные учреждения"],
                "item" => "Государственные органы"
            ],
        ];
        foreach ($tplArray as $row) {
            if ($inData[0] === $row["tpl"][0]) {
                if (count($row["tpl"]) === 1) {
                    return ($row["item"]);
                } else {
                    if (count($inData) > 1) {
                        if ($inData[1] === $row["tpl"][1]) {
                            return ($row["item"]);
                        }
                    }
                }
            }
        }
        return null;
    }

    public function getIndustry($text)
    {
        if (preg_match('~Отрасли:<\/b><\/td> <td>(.+?)<\/td>~', $text, $matches)) {
            $matches = explode("&gt;&gt;", $matches[1]);
            foreach ($matches as $key => $value) {
                $matches[$key] = trim($value);
            }
            return $this->calcIndustry($matches);
        }
        return false;
    }

    public function changeIndustry($htmlStr, $industry)
    {
        $result = preg_replace('/(Отрасли.+?<td>)(.*)(<\/td>)/U', '$1' . '<td>' . $industry . '</td>' . '$3', $htmlStr);
        return $result;
    }


    public function getOrganization($text)
    {
        $data = [
            "organization" => null,
            "city"         => null,
            "date"         => null,
        ];
        if (preg_match('~<h3>Внедрение</h3> <p>(.+?)<\/p>~', $text, $matches)) {
            $data["organization"] = trim(strip_tags($matches[1]));
        }
        // http://regex101.com/r/qB5vI8/2
        if (preg_match('~<h3>Внедрение<\/h3>(.+)<p>(.+),+\s+(.+?[0-9]{4})+\s?<\/p>~', $text, $matches)) {
            $city = trim(strip_tags($matches[2]));
            $city = explode(" ", $city);
            $city = end($city);
            $data["city"] = $city;
            $data["date"] = trim(strip_tags($matches[3]));
        }
        return $data;
    }

    public function getArmCount($text)
    {
        if (preg_match('~<p>Общее число автоматизированных рабочих мест: <b>([0-9]+)<\/b>~', $text, $matches)) {
            return trim($matches[1]);
        }
        return false;
    }

    /** @deprecated */
    public function getChildUlLi($dom, $li = null)
    {
        if (!$dom instanceof \simple_html_dom_node) {
            return null;
        }
        if ($li) {
            $elements = $dom->children();
            $i = 0;
            while ($elements[$i] !== $li) {
                $i++;
            };

            while ($elements[$i]->tag !== "ul") {
                $i++;
            }

            $ul = $elements[$i];
        } else {
            $ul = $dom->find("ul", 0);
        }
        if (!$ul) {
            return null;
        }
        $firstLi = $ul->find("li", 0);
        if (!$firstLi) {
            return null;
        }
        return [
            "ul"   => $ul,
            "li"   => $firstLi,
            "text" => $firstLi->plaintext,
        ];
    }

    public function getChildUlLi2($dom, $li = null)
    {
        if (!$dom instanceof \simple_html_dom_node) {
            return null;
        }
        if ($li) {
            $elements = $dom->children();
            $i = 0;
            while ($elements[$i] !== $li) {
                $i++;
            };

            while ($elements[$i]->tag !== "ul") {
                $i++;
            }

            $ul = $elements[$i];
        } else {
            $ul = $dom->find("ul", 0);
        }
        if (!$ul) {
            return null;
        }

        $lis = [];
        $elements = $ul->children();
        foreach($elements as $element) {
            if ($element->tag === "li") {
                $lis[] = $element;
            }
        }

        return [
            "ul"   => $ul,
            "lis"   => $lis,
        ];
    }

    public function getLiList($dom)
    {
        if (!$dom instanceof \simple_html_dom_node) {
            return null;
        }
        $ul = $dom->find("ul", 0);
        if (!$ul) {
            return [];
        }

        $elements = $ul->children();
        $firstLiList = [];
        foreach($elements as $element) {
            if ($element->tag === "li") {
                $firstLiList[] = $element;
            }
        }
        if (!$firstLiList) {
            return [];
        }
        return [
            "ul"     => $ul,
            "liList" => $firstLiList,
        ];
    }


    public function getFunctions($dom)
    {
        $functions = [];
        $liList = $this->getLiList($dom);
        if (!$liList) {
            return null;
        }
        $firstUl = $liList["ul"];
        $liList = $liList["liList"];

        foreach ($liList as $firstLi) {
            $firstText = $firstLi->plaintext;
            switch ($firstText) {
                case "Финансы, управленческий учет, мониторинг показателей":
                    $secondNode = $this->getChildUlLi2($firstUl, $firstLi);
                    $lis = $secondNode["lis"];
                    foreach($lis as $li) {
                        $secondText = $li->plaintext;
                        switch ($secondText) {
                            case "Учет бухгалтерский, налоговый, бюджетный, включая регламентированную отчетность":
                                $thirdNode = $this->getChildUlLi2($secondNode["ul"], $li);
                                $lis = $thirdNode["lis"];
                                foreach($lis as $li) {
                                    $thirdText = $li->plaintext;
                                    switch ($thirdText) {
                                        case "Бухгалтерский учет":
                                        case "Налоговый учет":
                                            $functions[] = "Бухгалтерский, налоговый учет";
                                            break;
                                        case "Бюджетный учет (для бюджетных учреждений)":
                                            $functions[] = "Бюджетный учет";
                                            break;
                                    }
                                }
                                break;
                            case "Бюджетирование, финансовое планирование":
                            case "Управленческий учет и расчет себестоимости методом ABC":
                            case "Управленческий учет":
                                $functions[] = "Управленческий учет и бюджетирование";
                                break;
                        }
                    }
                    break;
                case "Учет по международным и национальным стандартам":
                    $functions[] = "МСФО";
                    break;
                case "Управление отношениями с клиентами (CRM)":
                    $functions[] = "Управление продажами и отношениями с клиентами (CRM)";
                    break;
                case "Управление персоналом и кадровый учет (HRM)":
                    $functions[] = "Кадровый учет и зарплата (HRM)";
                    break;
                case "Управление продажами, логистикой и транспортом (SFM, WMS, TMS)":
                    $secondNode = $this->getChildUlLi2($firstUl, $firstLi);
                    $lis = $secondNode["lis"];
                    foreach($lis as $li) {
                        $secondText = $li->plaintext;
                        switch ($secondText) {
                            case "Склад и логистика":
                            case "Транспорт":
                                $functions[] = "Управление логистикой и транспортом";
                                break;
                            case "Продажи (сбыт), сервис, маркетинг":
                                $functions[] = "Управление продажами и отношениями с клиентами (CRM)";
                                break;
                        }
                    }
                    break;
                case "Закупки (снабжение) и управление отношениями с поставщиками":
                    $functions[] = "Управление продажами и отношениями с клиентами (CRM)";
                    break;
                case "Документооборот (ECM)":
                    $functions[] = "Документооборот";
                    break;
                case "Управление бизнес-процессами и ИТ-процессами ":
                    $functions[] = "Оптимизация бизнес-процессов";
                    break;
                case "Различная отраслевая специфика":
                    $secondNode = $this->getChildUlLi2($firstUl, $firstLi);
                    $lis = $secondNode["lis"];
                    foreach($lis as $li) {
                        $secondText = $li->plaintext;
                        switch ($secondText) {
                            case "Строительство" :
                                $functions[] = "Отраслевой учет";
                                break;
                            case "Производство, услуги" :
                                $functions[] = "Управление производством (ERP)";
                        }
                    }
                    break;
                case "Другое":
                    $secondNode = $this->getChildUlLi2($firstUl, $firstLi);
                    $lis = $secondNode["lis"];
                    foreach($lis as $li) {
                        $secondText = $li->plaintext;
                        switch ($secondText) {
                            case "Другое" :
                                $functions[] = "Отраслевой учет";
                                break;
                        }
                    }
                    break;
                case "Различная отраслевая специфика":
                    $secondNode = $this->getChildUlLi2($firstUl, $firstLi);
                    $lis = $secondNode["lis"];
                    foreach($lis as $li) {
                        $secondText = $li->plaintext;
                        switch ($secondText) {
                            case "Производство, услуги" :
                                $functions[] = "Управление производством (ERP)";
                                break;
                        }
                    }
                    break;
            }
        }
        $functions = array_unique($functions);
        return $functions;
    }

    public function changeFunctions($htmlStr, $functions)
    {
        $functions = implode(',', $functions);
        $result = preg_replace('/(<h3>Автоматизированы следующие функции:<\/h3>\s*<ul>)(.+?)(\s*<\/ul>\s*<h3>)/s', '$1' . '<li>' . $functions . '</li>' . '$3', $htmlStr);
        return $result;
    }

    public function getText($htmlStr, $industry, $functions)
    {
        $htmlStr = preg_replace("/<tr.+?Партнер, осуществивший внедрение\/проект.+?<\/tr>/s", "", $htmlStr);
        $htmlStr = preg_replace('/(<p((.+?)<a(.+?)class=\"navbar\">(.*?)<\/a>\s+)<\/p>)/', "", $htmlStr);
        $htmlStr = preg_replace('/(<p>\s+<a href=\"\.\/\?geo_id=\d+.+<\/p>)/', "", $htmlStr);
        $htmlStr = $this->changeIndustry($htmlStr, $industry);
        $htmlStr = $this->changeFunctions($htmlStr, $functions);
        return $htmlStr;
    }

    public function getTitle($dom)
    {
        $h1 = $dom->find("h1", 0);
        if (!$h1) {
            return null;
        }
        return $h1->plaintext;
    }

    public function getPType($htmlStr)
    {
        if (preg_match('/<h3>\s*?Тип\s+?проекта:(.+?)\s*?<\/h3>/', $htmlStr, $matches)) {
            return trim($matches[1]);
        }
        return false;
    }

    public function parseSolution($htmlStr)
    {
        $dom = $this->getDom($htmlStr);
        if (!$dom instanceof \simple_html_dom) {
            throw new \Exception("Error in get dom from string: " . htmlspecialchars($htmlStr));
        }
        $content = $dom->find("table#mainBodyTable td[height=100%]", 0);
        $table = $content->find("table[height=100%]", 0);
        $mainDom = $table->find("td", 4);
        $mainData = $this->prepareHtml($mainDom);

        $solution = new Solution();
        $solution->setTitle($this->getTitle($mainDom));
        $solution->setType($this->getTypeSolution($mainData));
        $solution->setIndustry($this->getIndustry($mainData));
        $dataOrg = $this->getOrganization($mainData);
        $solution->setOrganization($dataOrg["organization"]);
        $solution->setCity($dataOrg["city"]);
        $solution->setDate($dataOrg["date"]);
        $solution->setArms($this->getArmCount($mainData));
        $solution->setFunctions($this->getFunctions($mainDom));
        $text = $this->getText($mainData, $solution->getIndustry(), $solution->getFunctions());
        $solution->setText($text);
        $solution->setPtype($this->getPType($mainData));
        return $solution;
    }

    public function parseReview($htmlStr, $solutionId)
    {
        $htmlStr = $this->prepareHtml($htmlStr);
        $review = new Review();
        if (preg_match('/(<img[^<|>]*(\/home\/www\/www.1c.ru\/rus\/partners\/solutions\/responses).*<\/div>.*)(<p.*)(<p>\s+<\!.+1x1\.gif)/U', $htmlStr, $matches)) {
            $review->setText($matches[3]);
        }
        if (preg_match_all('/(<img[^<]*name=\S(\/home\/www\/www.1c.ru\/rus\/partners\/solutions\/responses\/[\w\/\.\-\_]+))/', $htmlStr, $matches, PREG_PATTERN_ORDER)) {
            $client = $this->getHttpClient();
            foreach ($matches[2] as $num => $url) {
                $url = "http://www.1c.ru" . $url;
                $num = $num + 1;
                $file = $client->saveFile($url, "review-{$solutionId}-{$num}");
                $file = strtolower($file);
                $review->addFile($file);
            }
        }
        return $review;
    }

} 
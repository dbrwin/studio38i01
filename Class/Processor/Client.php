<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Processor;


class Client 
{
    public function getHttpResponse($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
//        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

        $apiResponse = curl_exec($ch);
        $responseInformation = curl_getinfo($ch);
        curl_close($ch);

        if (intval($responseInformation['http_code']) == 200) {
            return $apiResponse;
        } else {
            return false;
        }
    }

    public function toUtf8($str)
    {
        return mb_convert_encoding($str, 'utf-8', 'windows-1251');;
    }

    public function getSolutionsList($type=1, $page=1)
    {
        $url = "http://www.1c.ru/rus/partners/solutions/solutions.jsp?PartID=985&v8only=1&cmk={$type}&isGroup=1&isNew=-1&parts={$page}";
        return $this->toUtf8($this->getHttpResponse($url));
    }

    public function getSolution($id)
    {
        $url = "http://www.1c.ru/rus/partners/solutions/solution.jsp?SolutionID={$id}";
        return $this->toUtf8($this->getHttpResponse($url));
    }

    public function getResponse($id)
    {
        $url = "http://www.1c.ru/rus/partners/solutions/response.jsp?solutionID={$id}";
        $result = $this->getHttpResponse($url);
        if (!$result) {
            return false;
        }
        $result = $this->toUtf8($result);
        if (strpos($result, "Неверные параметры запроса") !== false) {
            return false;
        }
        return $result;
    }

    public function saveFile($url, $name = null, $directory = null)
    {
        if (empty($directory)) {
            $directory = ROOT_DIR . "/public/reviews";
        }
        $ext = strtolower(pathinfo($url, PATHINFO_EXTENSION));
        if ($ext === "jpeg") {
            $ext = "jpg";
        }
        if (is_null($name)) {
            $name = uniqid("38file-", true);
        }
        $name = $name . "." .$ext;
        $path = $directory . "/" . $name;
        $file = $this->getHttpResponse($url);
        if (!$file) {
            return false;
        }
        if (file_put_contents($path, $file)) {
            return $name;
        }
    }

} 
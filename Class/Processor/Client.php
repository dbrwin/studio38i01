<?php
/**
 * User: Vasiliy Shvakin (orbisnull) zen4dev@gmail.com
 */

namespace Processor;


class Client 
{
    public function getResponse($url)
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

    public function getSolutionsList($type=1, $page=1)
    {
        $url = "http://www.1c.ru/rus/partners/solutions/solutions.jsp?PartID=985&v8only=1&cmk={$type}&isGroup=1&isNew=-1&parts={$page}";
        return $this->getResponse($url);
    }

    public function getSolution($id)
    {
        $url = "http://www.1c.ru/rus/partners/solutions/solution.jsp?SolutionID={$id}";
        return $this->getResponse($url);
    }

} 
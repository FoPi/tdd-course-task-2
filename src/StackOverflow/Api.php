<?php

namespace StackOverflow;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Api
 */
class Api
{
    /**
     * @var string
     */
    protected $baseUri = "https://api.stackexchange.com/2.2/";

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var int
     */
    protected $limit = 30;

    /**
     * @var array
     */
    protected $defaultParams = array(
        "site" => "stackoverflow",
        "order" => "desc",
        "sort" => "activity"
    );

    /**
     * Api constructor.
     */
    public function __construct()
    {
        $this->client = new Client(["base_uri" => $this->baseUri]);
    }

    /**
     * @param array $params
     * @param array $options
     *
     * @return array
     */
    public function getFeaturedQuestions(array $params = array(), array $options = array())
    {
        $url = $this->buildQuery("questions/featured", $params);
        $response = $this->client->request("GET", $url, $options);

        return $this->parseResponse($response);
    }

    /**
     * @param array $questionIds
     * @param array $params
     * @param $options
     *
     * @return mixed
     */
    public function getAnswersForQuestions(array $questionIds, array $params = array(), array $options = array())
    {
        $questionIdsString = join(";", $questionIds);
        $url = $this->buildQuery("questions/" . $questionIdsString . "/answers", $params);

        $response = $this->client->request("GET", $url, $options);

        return $this->parseResponse($response);
    }

    /**
     * @param $questionId
     *
     * @return array
     */
    public function getAllAnswersForQuestion($questionId)
    {
        $params = ["page" => 1];
        $response = [];

        do {
            $url = $this->buildQuery("questions/" . $questionId . "/answers", $params);
            $result = $this->parseResponse($this->client->request("GET", $url, $params));

            if (isset($result['items'])) {
                $this->appendArray($response, $result['items']);
                $params["page"]++;
            }
        } while ($result['has_more']);


        return $response;
    }

    /**
     * @param $resource
     * @param array $params
     *
     * @return string
     */
    protected function buildQuery($resource, array $params)
    {
        $params = array_merge($this->defaultParams, $params);

        if ($params['site'] !== "stackoverflow") {
            $params['site'] = "stackoverflow";
        }

        return $resource . "/?" . http_build_query($params);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return bool|mixed
     */
    protected function parseResponse(ResponseInterface $response)
    {
        if ($response->getStatusCode() === 200) {
            return \GuzzleHttp\json_decode($response->getBody()->getContents(), true);
        }

        return false;
    }

    /**
     * Because array_merge is really slow
     *
     * @param $array1
     * @param $array2
     */
    protected function appendArray(&$array1, $array2)
    {
        foreach ($array2 as $item) {
            $array1[] = $item;
        }
    }
}
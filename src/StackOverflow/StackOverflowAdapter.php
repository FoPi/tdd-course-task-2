<?php

namespace StackOverflow;

/**
 * Class StackOverflowAdapter
 */
class StackOverflowAdapter
{
    /**
     * @var Api
     */
    protected $api;

    /**
     * StackOverflowAdapter constructor.
     *
     * @param Api $api
     */
    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    /**
     * @return array
     */
    public function getTheMostPopularQuestion()
    {
        $response = $this->api->getFeaturedQuestions(['pagesize' => 1]);

        if ($this->hasResult($response)) {
            return $response['items'][0];
        }

        return [];
    }

    public function getAnswersUserIdsForTheMostPopularQuestion()
    {
        $result = $this->getTheMostPopularQuestion();

        if (!$result || $result['answer_count'] === 0) {
            return [];
        }

        $answers = $this->api->getAllAnswersForQuestion($result['question_id']);

        return $this->collectUserIdsForQuestionAnswers($answers);
    }

    /**
     * @param array $result
     *
     * @return array
     */
    protected function collectUserIdsForQuestionAnswers(array $result)
    {
        $userIds = [];

        foreach ($result as $answer) {
            $userIds[] = $answer['owner']['user_id'];
        }

        return $userIds;
    }

    /**
     * @param $result
     *
     * @return bool
     */
    protected function hasResult($result):bool
    {
        return isset($result['items']) && count($result['items']);
    }
}
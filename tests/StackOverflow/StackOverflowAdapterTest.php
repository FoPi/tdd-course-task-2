<?php

namespace Tests\StackOverflow;

use StackOverflow\Api;
use StackOverflow\StackOverflowAdapter;

class StackOverflowAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $apiPopularQuestionResponse = [
        "items" => [
            [
                "tags" => [
                    "node.js",
                    "express",
                    "connect"
                ],
                "owner" => [
                    "reputation" => 88160,
                    "user_id" => 362536,
                    "user_type" => "registered",
                    "accept_rate" => 90,
                    "profile_image" => "https://www.gravatar.com/avatar/da5816365344305a614343a4065a2786?s=128&d=identicon&r=PG",
                    "display_name" => "Brad",
                    "link" => "http://stackoverflow.com/users/362536/brad"
                ],
                "is_answered" => true,
                "view_count" => 1477,
                "bounty_amount" => 50,
                "bounty_closes_date" => 1479395930,
                "accepted_answer_id" => 18603110,
                "answer_count" => 2,
                "score" => 5,
                "last_activity_date" => 1479060476,
                "creation_date" => 1378247636,
                "question_id" => 18602578,
                "link" => "http://stackoverflow.com/questions/18602578/proper-way-to-remove-middleware-from-the-express-stack",
                "title" => "Proper way to remove middleware from the Express stack?"
            ]
        ],
        "has_more" => true,
        "quota_max" => 300,
        "quota_remaining" => 299
    ];

    /**
     * @var array
     */
    protected $apiUnansweredPopularQuestionResponse = [
        "tags" => [
            "node.js",
            "express",
            "connect"
        ],
        "owner" => [
            "reputation" => 88160,
            "user_id" => 362536,
            "user_type" => "registered",
            "accept_rate" => 90,
            "profile_image" => "https://www.gravatar.com/avatar/da5816365344305a614343a4065a2786?s=128&d=identicon&r=PG",
            "display_name" => "Brad",
            "link" => "http://stackoverflow.com/users/362536/brad"
        ],
        "is_answered" => true,
        "view_count" => 1477,
        "bounty_amount" => 50,
        "bounty_closes_date" => 1479395930,
        "accepted_answer_id" => 18603110,
        "answer_count" => 0,
        "score" => 5,
        "last_activity_date" => 1479060476,
        "creation_date" => 1378247636,
        "question_id" => 18602578,
        "link" => "http://stackoverflow.com/questions/18602578/proper-way-to-remove-middleware-from-the-express-stack",
        "title" => "Proper way to remove middleware from the Express stack?"
    ];

    /**
     * @var array
     */
    protected $mostPopularQuestion = [
        "tags" => [
            "node.js",
            "express",
            "connect"
        ],
        "owner" => [
            "reputation" => 88160,
            "user_id" => 362536,
            "user_type" => "registered",
            "accept_rate" => 90,
            "profile_image" => "https://www.gravatar.com/avatar/da5816365344305a614343a4065a2786?s=128&d=identicon&r=PG",
            "display_name" => "Brad",
            "link" => "http://stackoverflow.com/users/362536/brad"
        ],
        "is_answered" => true,
        "view_count" => 1477,
        "bounty_amount" => 50,
        "bounty_closes_date" => 1479395930,
        "accepted_answer_id" => 18603110,
        "answer_count" => 2,
        "score" => 5,
        "last_activity_date" => 1479060476,
        "creation_date" => 1378247636,
        "question_id" => 18602578,
        "link" => "http://stackoverflow.com/questions/18602578/proper-way-to-remove-middleware-from-the-express-stack",
        "title" => "Proper way to remove middleware from the Express stack?"
    ];

    /**
     * @var int
     */
    protected $mostPopularQuestionId = 18602578;

    /**
     * @var array
     */
    protected $apiAnswersResponse = [
        [
            "owner" => [
                "reputation" => 2606,
                "user_id" => 3716153,
                "user_type" => "registered",
                "profile_image" => "https://www.gravatar.com/avatar/c4dd71e3def4643c045caabcf8056645?s=128&d=identicon&r=PG&f=1",
                "display_name" => "Gaafar",
                "link" => "http://stackoverflow.com/users/3716153/gaafar"
            ],
            "is_accepted" => false,
            "score" => 0,
            "last_activity_date" => 1479060476,
            "creation_date" => 1479060476,
            "answer_id" => 40577094,
            "question_id" => 18602578
        ],
        [
            "owner" => [
                "reputation" => 73146,
                "user_id" => 201952,
                "user_type" => "registered",
                "accept_rate" => 66,
                "profile_image" => "https://www.gravatar.com/avatar/db63f548a33a1d7d9abc06c0442caabd?s=128&d=identicon&r=PG",
                "display_name" => "josh3736",
                "link" => "http://stackoverflow.com/users/201952/josh3736"
            ],
            "is_accepted" => true,
            "score" => 11,
            "last_activity_date" => 1378251220,
            "creation_date" => 1378251220,
            "answer_id" => 18603110,
            "question_id" => 18602578
        ]
    ];

    /**
     * @var array
     */
    protected $answersUserIds = [3716153, 201952];

    /**
     * @var array
     */
    protected $defaultResult = [
        "items" => [],
        "has_more" => false,
        "quota_max" => 300,
        "quota_remaining" => 299
    ];

    /**
     * @var array
     */
    protected $app;

    /**
     * @var StackOverflowAdapter
     */
    protected $adapter;

    /**
     *
     */
    public function setUp()
    {
        $this->app["stack_overflow.api"] = $this->getMockBuilder(Api::class)
            ->disableOriginalConstructor()
            ->setMethods(["getFeaturedQuestions", "getAnswersForQuestions", "getAllAnswersForQuestion"])
            ->getMock();

        $this->adapter = new StackOverflowAdapter($this->app["stack_overflow.api"]);
    }

    /**
     *
     */
    public function testGetTheMostPopularQuestion()
    {
        $this->app["stack_overflow.api"]
            ->expects($this->once())
            ->method("getFeaturedQuestions")
            ->with($this->equalTo(["pagesize" => 1]))
            ->will($this->returnValue($this->apiPopularQuestionResponse));

        $this->app['stack_overflow.api']
            ->expects($this->never())
            ->method("getAnswersForQuestions");

        $this->app['stack_overflow.api']
            ->expects($this->never())
            ->method("getAllAnswersForQuestion");

        $this->assertEquals($this->mostPopularQuestion, $this->adapter->getTheMostPopularQuestion());
    }

    /**
     *
     */
    public function testEmptyQuestionResult()
    {
        $this->app["stack_overflow.api"]
            ->expects($this->once())
            ->method("getFeaturedQuestions")
            ->will($this->returnValue($this->defaultResult));

        $this->app['stack_overflow.api']
            ->expects($this->never())
            ->method("getAllAnswersForQuestion");

        $this->assertEquals([], $this->adapter->getTheMostPopularQuestion());
    }

    /**
     *
     */
    public function testMostPopularQuestionAnswersUserIdsWithEmptyQuestionResponse()
    {
        $this->app["stack_overflow.api"]
            ->expects($this->once())
            ->method("getFeaturedQuestions")
            ->with($this->equalTo(["pagesize" => 1]))
            ->will($this->returnValue([]));

        $this->app['stack_overflow.api']
            ->expects($this->never())
            ->method("getAllAnswersForQuestion");

        $this->assertEquals([], $this->adapter->getAnswersUserIdsForTheMostPopularQuestion());
    }

    /**
     *
     */
    public function testMostPopularQuestionUnAnswered()
    {
        $this->app["stack_overflow.api"]
            ->expects($this->once())
            ->method("getFeaturedQuestions")
            ->with($this->equalTo(["pagesize" => 1]))
            ->will($this->returnValue($this->apiUnansweredPopularQuestionResponse));

        $this->app['stack_overflow.api']
            ->expects($this->never())
            ->method("getAllAnswersForQuestion");

        $this->assertEquals([], $this->adapter->getAnswersUserIdsForTheMostPopularQuestion());
    }

    /**
     *
     */
    public function testMostPopularQuestionAnswersUserIds()
    {
        $this->app["stack_overflow.api"]
            ->expects($this->once())
            ->method("getFeaturedQuestions")
            ->with($this->equalTo(["pagesize" => 1]))
            ->will($this->returnValue($this->apiPopularQuestionResponse));

        $this->app['stack_overflow.api']
            ->expects($this->once())
            ->method("getAllAnswersForQuestion")
            ->with($this->equalTo($this->mostPopularQuestionId))
            ->willReturn($this->apiAnswersResponse);

        $this->assertEquals($this->answersUserIds, $this->adapter->getAnswersUserIdsForTheMostPopularQuestion());
    }
}

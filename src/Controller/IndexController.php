<?php

declare(strict_types=1);

namespace App\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 首页
 *
 * Class IndexController
 * @package App\Controller
 */
class IndexController extends Controller
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function pageIndex(Request $request, Response $response): Response
    {
        return $response->withJson(['t' => 'f']);
    }

    /**
     * 执行数据库迁移，迁移前先把配置文件中的 数据库前缀 删除
     *
     * option 表需要手动迁移
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function migration(Request $request, Response $response): Response
    {
        /** @var \Medoo\Medoo $db */
        $db = $this->container->get(\Medoo\Medoo::class);

        // answer 表迁移
        $answers = $db->select('md_answer', '*');
        foreach ($answers as &$answer) {
            unset($answer['status']);
            $answer['delete_time'] = 0;
        }
        $db->insert('mc_answer', $answers);

        // question 表迁移
        $questions = $db->select('md_question', '*');
        foreach ($questions as &$question) {
            $question['last_answer_time'] = $question['answer_time'];
            $question['delete_time'] = 0;
            unset($question['answer_time']);
            unset($question['status']);
            unset($question['topic_id']);
        }
        $db->insert('mc_question', $questions);

        // question_follow 和 user_follow 表迁移
        $questionFollows = $db->select('md_question_follow', '*');
        $userFollows = $db->select('md_user_follow', '*');

        foreach ($questionFollows as &$questionFollow) {
            $questionFollow['followable_id'] = $questionFollow['question_id'];
            $questionFollow['followable_type'] = 'question';
            unset($questionFollow['question_follow_id']);
            unset($questionFollow['question_id']);
        }

        foreach ($userFollows as &$userFollow) {
            $userFollow['followable_id'] = $userFollow['target_user_id'];
            $userFollow['followable_type'] = 'user';
            unset($userFollow['user_follow_id']);
            unset($userFollow['target_user_id']);
        }

        $db->insert('mc_followable', $questionFollows);
        $db->insert('mc_followable', $userFollows);

        // user 表迁移
        $users = $db->select('md_user', '*');
        foreach ($users as &$user) {
            unset($user['mobile']);
            $user['last_login_time'] = $user['login_time'];
            unset($user['login_time']);
            $user['last_login_ip'] = $user['login_ip'];
            unset($user['login_ip']);
            unset($user['active_time']);
            $user['following_user_count'] = $user['following_count'];
            unset($user['following_count']);
            $user['following_article_count'] = 0;
            $user['following_question_count'] = 0;
            $user['following_topic_count'] = 0;
            $user['bio'] = '';
            $user['blog'] = '';
            $user['company'] = '';
            $user['location'] = '';
            unset($user['status']);
            $user['update_time'] = $user['create_time'];
            $user['delete_time'] = 0;
        }
        $db->insert('mc_user', $users);


        return $response;
    }
}

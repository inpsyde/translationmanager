<?php # -*- coding: utf-8 -*-

namespace Translationmanager\Tests\Integration;

use Brain\Monkey\Functions;
use Translationmanager\Api;
use Translationmanager\Api\Project;
use Translationmanager\Tests\TestCase;

/**
 * Class ProjectTest
 *
 * @package Translationmanager\Tests\Integration
 */
class ProjectTest extends TestCase
{

    /**
     * Everything is based on the boolean value returned by `wp_remote_request`
     * True means the `wp_remote_request` received the correct data and everything gone fine.
     *
     * So, `wp_remote_retrieve_response_code` returns 200 only and if only `wp_remote_request` returned true.
     * The same for `wp_remote_retrieve_body` that return id = 1 if response is true, 0 otherwise.
     */
    public function testThatCreateProjectSuccess()
    {

        Functions\expect('wp_remote_request')
            ->once()
            ->andReturnUsing(function ($url, $array) {

                if (
                    $url === 'https://sandbox.api.eurotext.de/api/v1/project.json'
                    && $array['method'] === 'POST'
                    && empty(array_diff($array['headers'], [
                        'X-System' => 'WordPress',
                        'X-System-Version' => '1.0.0',
                        'X-Plugin' => 'translationmanager',
                        'X-Plugin-Version' => '1.0.0-test',
                        'X-Name' => 'unit-test',
                        'X-Type' => 'quote',
                        'X-Callback' => null,
                        'Content-Type' => 'application/json',
                        'plugin_key' => 'b37270d25d5b3fccf137f7462774fe76',
                        'apikey' => 'mykey',
                    ]))
                    && $array['body'] === '[]'
                ) {
                    return [
                        'body' => '{"id":1}',
                        'response' => [
                            'code' => 200,
                            'message' => 'OK',
                        ],
                    ];
                }
            });

        $api = new Api(
            'mykey',
            'b37270d25d5b3fccf137f7462774fe76',
            'https://sandbox.api.eurotext.de/api/v1'
        );

        $project = new Project($api);
        $domainProject = new \Translationmanager\Domain\Project(
            'WordPress',
            '1.0.0',
            'translationmanager',
            '1.0.0-test',
            'unit-test'
        );

        $id = $project->create($domainProject);

        $this->assertSame(1, $id);
    }

    /**
     * Null means everything ok because the PATCH request doesn't have any body to returns.
     */
    public function testThatUpdateStatusSuccess()
    {

        Functions\expect('wp_remote_request')
            ->once()
            ->andReturnUsing(function ($url, $data) {

                if ('https://sandbox.api.eurotext.de/api/v1/project/1.json' == $url
                    && 'new' === $data['headers']['X-Item-Status']
                ) {
                    return [
                        'body' => '',
                        'response' => [
                            'code' => 204,
                            'message' => '',
                        ],
                    ];
                }
            });

        $api = new Api(
            'mykey',
            'b37270d25d5b3fccf137f7462774fe76',
            'https://sandbox.api.eurotext.de/api/v1'
        );

        $project = new Project($api);

        $response = $project->update_status(1, 'new');

        $this->assertNull($response);
    }

    /**
     * @inheritDoc
     */
    protected function setUp()
    {

        parent::setUp();

        require_once getenv('TESTS_PATH') . '/stubs/commonStubs.php';
        require_once getenv('TESTS_PATH') . '/stubs/wpRemoteStubs.php';

        Functions\when('esc_html__')
            ->returnArg(1);
    }
}

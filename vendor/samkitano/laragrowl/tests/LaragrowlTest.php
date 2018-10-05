<?php

use Kitano\Laragrowl\LaragrowlNotifier as LN;
use Mockery as m;

class LaragrowlTest extends PHPUnit_Framework_TestCase
{

    protected $session;

    protected $laragrowl;


    /**
     * Test setup
     */
    public function setUp()
    {
        $this->session   = m::mock('Kitano\Laragrowl\SessionStore');
        $this->laragrowl = new LN($this->session);
    }

    /** @test */
    public function it_displays_one_single_default_notification_using_the_message_method_and_no_arguments_passed()
    {
        $this->session->shouldReceive('read')->with('laragrowl_messages')->once()->andReturn([]);
        $this->session->shouldReceive('write')->with('laragrowl_messages', [
            [
                'message' => "Tests Laragrowl::message('My Message')",
                'type'    => 'default',
                'header'  => '',
                'sticky'  => false,
                'life'    => 6000,
                'group'   => ''
            ]
        ]);

        $this->laragrowl->message("Tests Laragrowl::message('My Message')");
    }

    /** @test */
    public function it_displays_one_single_notification_using_the_message_method_and_type_argument_passed_as_info()
    {
        $this->session->shouldReceive('read')->with('laragrowl_messages')->once()->andReturn([]);
        $this->session->shouldReceive('write')->with('laragrowl_messages', [
            [
                'message' => "Tests Laragrowl::message('My Message', 'info')",
                'type'    => 'info',
                'header'  => '',
                'sticky'  => false,
                'life'    => 6000,
                'group'   => ''
            ]
        ]);

        $this->laragrowl->message("Tests Laragrowl::message('My Message', 'info')", 'info');
    }

    /** @test */
    public function it_displays_one_single_notification_using_the_message_method_and_type_argument_passed_as_success()
    {
        $this->session->shouldReceive('read')->with('laragrowl_messages')->once()->andReturn([]);
        $this->session->shouldReceive('write')->with('laragrowl_messages', [
            [
                'message' => "Tests Laragrowl::message('My Message', 'success')",
                'type'    => 'success',
                'header'  => '',
                'sticky'  => false,
                'life'    => 6000,
                'group'   => ''
            ]
        ]);

        $this->laragrowl->message("Tests Laragrowl::message('My Message', 'success')", 'success');
    }

    /** @test */
    public function it_displays_one_single_notification_using_the_message_method_and_type_argument_passed_as_warning()
    {
        $this->session->shouldReceive('read')->with('laragrowl_messages')->once()->andReturn([]);
        $this->session->shouldReceive('write')->with('laragrowl_messages', [
            [
                'message' => "Tests Laragrowl::message('My Message', 'warning')",
                'type'    => 'warning',
                'header'  => '',
                'sticky'  => false,
                'life'    => 6000,
                'group'   => ''
            ]
        ]);

        $this->laragrowl->message("Tests Laragrowl::message('My Message', 'warning')", 'warning');
    }

    /** @test */
    public function it_displays_one_single_notification_using_the_message_method_and_type_argument_passed_as_error()
    {
        $this->session->shouldReceive('read')->with('laragrowl_messages')->once()->andReturn([]);
        $this->session->shouldReceive('write')->with('laragrowl_messages', [
            [
                'message' => "Tests Laragrowl::message('My Message', 'error')",
                'type'    => 'error',
                'header'  => '',
                'sticky'  => false,
                'life'    => 6000,
                'group'   => ''
            ]
        ]);

        $this->laragrowl->message("Tests Laragrowl::message('My Message', 'error')", 'error');
    }

    /** @test */
    public function it_displays_one_single_default_notification_using_the_message_method_and_a_wrong_kind_of_type()
    {
        $this->session->shouldReceive('read')->with('laragrowl_messages')->once()->andReturn([]);
        $this->session->shouldReceive('write')->with('laragrowl_messages', [
            [
                'message' => "Tests Laragrowl::message('My Message', 'wrong')",
                'type'    => 'default',
                'header'  => '',
                'sticky'  => false,
                'life'    => 6000,
                'group'   => ''
            ]
        ]);

        $this->laragrowl->message("Tests Laragrowl::message('My Message', 'wrong')", 'wrong');
    }

    /** @test */
    public function it_displays_one_single_default_notification_using_the_message_method_and_a_null_type()
    {
        $this->session->shouldReceive('read')->with('laragrowl_messages')->once()->andReturn([]);
        $this->session->shouldReceive('write')->with('laragrowl_messages', [
            [
                'message' => "Tests Laragrowl::message('My Message', null)",
                'type'    => 'default',
                'header'  => '',
                'sticky'  => false,
                'life'    => 6000,
                'group'   => ''
            ]
        ]);

        $this->laragrowl->message("Tests Laragrowl::message('My Message', null)", null);
    }

    /** @test */
    public function it_displays_one_single_default_notification_using_the_message_method_and_header_argument_passed()
    {
        $this->session->shouldReceive('read')->with('laragrowl_messages')->once()->andReturn([]);
        $this->session->shouldReceive('write')->with('laragrowl_messages', [
            [
                'message' => "Tests Laragrowl::message('My Message', '', 'My Header')",
                'type'    => 'default',
                'header'  => 'My Header',
                'sticky'  => false,
                'life'    => 6000,
                'group'   => ''
            ]
        ]);

        $this->laragrowl->message("Tests Laragrowl::message('My Message', '', 'My Header')", '', 'My Header');
    }

    /** @test */
    public function it_displays_one_single_default_notification_using_the_message_method_and_header_argument_passed_as_null()
    {
        $this->session->shouldReceive('read')->with('laragrowl_messages')->once()->andReturn([]);
        $this->session->shouldReceive('write')->with('laragrowl_messages', [
            [
                'message' => "Tests Laragrowl::message('My Message', null, null)",
                'type'    => 'default',
                'header'  => '',
                'sticky'  => false,
                'life'    => 6000,
                'group'   => ''
            ]
        ]);

        $this->laragrowl->message("Tests Laragrowl::message('My Message', null, null)", null, null);
    }

    /** @test */
    public function it_displays_one_single_default_notification_using_the_message_method_and_sticky_argument_passed_as_true()
    {
        $this->session->shouldReceive('read')->with('laragrowl_messages')->once()->andReturn([]);
        $this->session->shouldReceive('write')->with('laragrowl_messages', [
            [
                'message' => "Tests Laragrowl::message('My Message', null, null, true)",
                'type'    => 'default',
                'header'  => '',
                'sticky'  => true,
                'life'    => 6000,
                'group'   => ''
            ]
        ]);

        $this->laragrowl->message("Tests Laragrowl::message('My Message', null, null, true)", null, null, true);
    }

    /** @test */
    public function it_displays_one_single_default_notification_using_the_message_method_and_a_wrong_sticky_argument()
    {
        $this->session->shouldReceive('read')->with('laragrowl_messages')->once()->andReturn([]);
        $this->session->shouldReceive('write')->with('laragrowl_messages', [
            [
                'message' => "Tests Laragrowl::message('My Message', null, null, 'not_sticky')",
                'type'    => 'default',
                'header'  => '',
                'sticky'  => false,
                'life'    => 6000,
                'group'   => ''
            ]
        ]);

        $this->laragrowl->message("Tests Laragrowl::message('My Message', null, null, 'not_sticky')", null, null, 'not_sticky');
    }

    /** @test */
    public function it_displays_one_single_default_notification_using_the_message_method_and_a_sticky_argument_as_string_sticky()
    {
        $this->session->shouldReceive('read')->with('laragrowl_messages')->once()->andReturn([]);
        $this->session->shouldReceive('write')->with('laragrowl_messages', [
            [
                'message' => "Tests Laragrowl::message('My Message', null, null, 'sticky')",
                'type'    => 'default',
                'header'  => '',
                'sticky'  => true,
                'life'    => 6000,
                'group'   => ''
            ]
        ]);

        $this->laragrowl->message("Tests Laragrowl::message('My Message', null, null, 'sticky')", null, null, 'sticky');
    }

    /** @test */
    public function it_displays_one_single_default_notification_using_the_message_method_and_a_sticky_argument_as_string_true()
    {
        $this->session->shouldReceive('read')->with('laragrowl_messages')->once()->andReturn([]);
        $this->session->shouldReceive('write')->with('laragrowl_messages', [
            [
                'message' => "Tests Laragrowl::message('My Message', null, null, 'true')",
                'type'    => 'default',
                'header'  => '',
                'sticky'  => true,
                'life'    => 6000,
                'group'   => ''
            ]
        ]);

        $this->laragrowl->message("Tests Laragrowl::message('My Message', null, null, 'true')", null, null, 'true');
    }

    /** @test */
    public function it_displays_one_single_default_notification_using_the_message_method_and_a_different_life_span()
    {
        $this->session->shouldReceive('read')->with('laragrowl_messages')->once()->andReturn([]);
        $this->session->shouldReceive('write')->with('laragrowl_messages', [
            [
                'message' => "Tests Laragrowl::message('My Message', null, null, null, 4000)",
                'type'    => 'default',
                'header'  => '',
                'sticky'  => false,
                'life'    => 4000,
                'group'   => ''
            ]
        ]);

        $this->laragrowl->message("Tests Laragrowl::message('My Message', null, null, null, 4000)", null, null, null, 4000);
    }

    /** @test */
    public function it_displays_one_single_default_notification_using_the_message_method_and_a_wrong_life_span()
    {
        $this->session->shouldReceive('read')->with('laragrowl_messages')->once()->andReturn([]);
        $this->session->shouldReceive('write')->with('laragrowl_messages', [
            [
                'message' => "Tests Laragrowl::message('My Message', null, null, null, 'lifespan')",
                'type'    => 'default',
                'header'  => '',
                'sticky'  => false,
                'life'    => 6000,
                'group'   => ''
            ]
        ]);

        $this->laragrowl->message("Tests Laragrowl::message('My Message', null, null, null, 'lifespan')", null, null, null, 'lifespan');
    }

    /** @test */
    public function it_displays_one_single_default_notification_using_the_message_method_and_a_string_as_life_span()
    {
        $this->session->shouldReceive('read')->with('laragrowl_messages')->once()->andReturn([]);
        $this->session->shouldReceive('write')->with('laragrowl_messages', [
            [
                'message' => "Tests Laragrowl::message('My Message', null, null, null, '3000')",
                'type'    => 'default',
                'header'  => '',
                'sticky'  => false,
                'life'    => 3000,
                'group'   => ''
            ]
        ]);

        $this->laragrowl->message("Tests Laragrowl::message('My Message', null, null, null, '3000')", null, null, null, '3000');
    }

    /** @test */
    public function it_displays_one_single_default_notification_using_the_message_method_and_a_group_argument()
    {
        $this->session->shouldReceive('read')->with('laragrowl_messages')->once()->andReturn([]);
        $this->session->shouldReceive('write')->with('laragrowl_messages', [
            [
                'message' => "Tests Laragrowl::message('My Message', null, null, null, null, 'My_group')",
                'type'    => 'default',
                'header'  => '',
                'sticky'  => false,
                'life'    => 6000,
                'group'   => 'My_group'
            ]
        ]);

        $this->laragrowl->message("Tests Laragrowl::message('My Message', null, null, null, null, 'My_group')", null, null, null, null, 'My_group');
    }

    /** @test */
    public function it_displays_one_single_default_notification_using_the_message_method_and_all_arguments_passed()
    {
        $this->session->shouldReceive('read')->with('laragrowl_messages')->once()->andReturn([]);
        $this->session->shouldReceive('write')->with('laragrowl_messages', [
            [
                'message' => "Tests Laragrowl::message('My Message', 'default', 'My Header', true, 200, 'My_group')",
                'type'    => 'default',
                'header'  => 'My Header',
                'sticky'  => true,
                'life'    => 200,
                'group'   => 'My_group'
            ]
        ]);

        $this->laragrowl->message("Tests Laragrowl::message('My Message', 'default', 'My Header', true, 200, 'My_group')", 'default', 'My Header', true, 200, 'My_group');
    }

    /** @test */
    public function it_displays_one_single_info_notification_using_the_info_method_and_no_arguments_passed()
    {
        $this->session->shouldReceive('read')->with('laragrowl_messages')->once()->andReturn([]);
        $this->session->shouldReceive('write')->with('laragrowl_messages', [
            [
                'message' => "Tests Laragrowl::info('My Message')",
                'type'    => 'info',
                'header'  => '',
                'sticky'  => false,
                'life'    => 6000,
                'group'   => ''
            ]
        ]);

        $this->laragrowl->info("Tests Laragrowl::info('My Message')");
    }

    /** @test */
    public function it_displays_one_single_info_notification_using_the_info_method_and_arguments_passed()
    {
        $this->session->shouldReceive('read')->with('laragrowl_messages')->once()->andReturn([]);
        $this->session->shouldReceive('write')->with('laragrowl_messages', [
            [
                'message' => "Tests Laragrowl::info('My Message'), 'My Header', 'sticky'",
                'type'    => 'info',
                'header'  => 'My Header',
                'sticky'  => true,
                'life'    => 6000,
                'group'   => ''
            ]
        ]);

        $this->laragrowl->info("Tests Laragrowl::info('My Message'), 'My Header', 'sticky'", 'My Header', 'sticky');
    }

    /** @test */
    public function it_displays_one_single_success_notification_using_the_success_method_and_no_arguments_passed()
    {
        $this->session->shouldReceive('read')->with('laragrowl_messages')->once()->andReturn([]);
        $this->session->shouldReceive('write')->with('laragrowl_messages', [
            [
                'message' => "Tests Laragrowl::success('My Message')",
                'type'    => 'success',
                'header'  => '',
                'sticky'  => false,
                'life'    => 6000,
                'group'   => ''
            ]
        ]);

        $this->laragrowl->success("Tests Laragrowl::success('My Message')");
    }

    /** @test */
    public function it_displays_one_single_success_notification_using_the_success_method_and_arguments_passed()
    {
        $this->session->shouldReceive('read')->with('laragrowl_messages')->once()->andReturn([]);
        $this->session->shouldReceive('write')->with('laragrowl_messages', [
            [
                'message' => "Tests Laragrowl::success('My Message'), 'My Header', 'sticky'",
                'type'    => 'success',
                'header'  => 'My Header',
                'sticky'  => true,
                'life'    => 6000,
                'group'   => ''
            ]
        ]);

        $this->laragrowl->success("Tests Laragrowl::success('My Message'), 'My Header', 'sticky'", 'My Header', 'sticky');
    }

    /** @test */
    public function it_displays_one_single_warning_notification_using_the_warning_method_and_no_arguments_passed()
    {
        $this->session->shouldReceive('read')->with('laragrowl_messages')->once()->andReturn([]);
        $this->session->shouldReceive('write')->with('laragrowl_messages', [
            [
                'message' => "Tests Laragrowl::warning('My Message')",
                'type'    => 'warning',
                'header'  => '',
                'sticky'  => false,
                'life'    => 6000,
                'group'   => ''
            ]
        ]);

        $this->laragrowl->warning("Tests Laragrowl::warning('My Message')");
    }

    /** @test */
    public function it_displays_one_single_warning_notification_using_the_warning_method_and_arguments_passed()
    {
        $this->session->shouldReceive('read')->with('laragrowl_messages')->once()->andReturn([]);
        $this->session->shouldReceive('write')->with('laragrowl_messages', [
            [
                'message' => "Tests Laragrowl::warning('My Message'), 'My Header', 'sticky'",
                'type'    => 'warning',
                'header'  => 'My Header',
                'sticky'  => true,
                'life'    => 6000,
                'group'   => ''
            ]
        ]);

        $this->laragrowl->warning("Tests Laragrowl::warning('My Message'), 'My Header', 'sticky'", 'My Header', 'sticky');
    }

    /** @test */
    public function it_displays_one_single_error_notification_using_the_error_method_and_no_arguments_passed()
    {
        $this->session->shouldReceive('read')->with('laragrowl_messages')->once()->andReturn([]);
        $this->session->shouldReceive('write')->with('laragrowl_messages', [
            [
                'message' => "Tests Laragrowl::error('My Message')",
                'type'    => 'error',
                'header'  => '',
                'sticky'  => false,
                'life'    => 6000,
                'group'   => ''
            ]
        ]);

        $this->laragrowl->error("Tests Laragrowl::error('My Message')");
    }

    /** @test */
    public function it_displays_one_single_error_notification_using_the_error_method_and_arguments_passed()
    {
        $this->session->shouldReceive('read')->with('laragrowl_messages')->once()->andReturn([]);
        $this->session->shouldReceive('write')->with('laragrowl_messages', [
            [
                'message' => "Tests Laragrowl::error('My Message'), 'My Header', 'sticky'",
                'type'    => 'error',
                'header'  => 'My Header',
                'sticky'  => true,
                'life'    => 6000,
                'group'   => ''
            ]
        ]);

        $this->laragrowl->error("Tests Laragrowl::error('My Message'), 'My Header', 'sticky'", 'My Header', 'sticky');
    }

}

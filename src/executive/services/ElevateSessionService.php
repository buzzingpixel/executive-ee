<?php
declare(strict_types=1);

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2018 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace buzzingpixel\executive\services;

use EE_Loader;
use EE_Router;
use EE_Session;
use buzzingpixel\executive\factories\QueryBuilderFactory;

/**
 * Class ElevateSessionService
 */
class ElevateSessionService
{
    /** @var QueryBuilderFactory $queryBuilderFactory */
    private $queryBuilderFactory;

    /** @var EE_Session $session */
    private $session;

    /** @var EE_Router $router */
    private $router;

    /** @var EE_Loader $load */
    private $load;

    /**
     * ElevateSessionService constructor
     * @param QueryBuilderFactory $queryBuilderFactory
     * @param EE_Session $session
     * @param EE_Router $router
     * @param EE_Loader $load
     */
    public function __construct(
        QueryBuilderFactory $queryBuilderFactory,
        EE_Session $session,
        EE_Router $router,
        EE_Loader $load
    ) {
        $this->queryBuilderFactory = $queryBuilderFactory;
        $this->session = $session;
        $this->router = $router;
        $this->load = $load;
    }

    /**
     * Elevates the session to SuperAdmin
     */
    public function run(): void
    {
        // Get the first SuperAdmin available
        $member = $this->queryBuilderFactory->make()->where('group_id', 1)
            ->limit(1)
            ->get('members')
            ->row();

        $this->session->create_new_session($member->member_id, true, true);

        foreach ($member as $key => $val) {
            if (! isset($this->session->userdata[$key])) {
                continue;
            }

            $this->session->userdata[$key] = $val;
        }

        // Give CLI user keys to the kingdom
        $this->session->userdata['is_banned'] = false;
        $this->session->userdata['can_view_offline_system'] = 'y';
        $this->session->userdata['can_access_cp'] = 'y';
        $this->session->userdata['can_access_content'] = 'y';
        $this->session->userdata['can_access_publish'] = 'y';
        $this->session->userdata['can_access_edit'] = 'y';
        $this->session->userdata['can_access_files'] = 'y';
        $this->session->userdata['can_access_fieldtypes'] = 'y';
        $this->session->userdata['can_access_design'] = 'y';
        $this->session->userdata['can_access_addons'] = 'y';
        $this->session->userdata['can_access_modules'] = 'y';
        $this->session->userdata['can_access_extensions'] = 'y';
        $this->session->userdata['can_access_accessories'] = 'y';
        $this->session->userdata['can_access_plugins'] = 'y';
        $this->session->userdata['can_access_members'] = 'y';
        $this->session->userdata['can_access_admin'] = 'y';
        $this->session->userdata['can_access_sys_prefs'] = 'y';
        $this->session->userdata['can_access_content_prefs'] = 'y';
        $this->session->userdata['can_access_tools'] = 'y';
        $this->session->userdata['can_access_comm'] = 'y';
        $this->session->userdata['can_access_utilities'] = 'y';
        $this->session->userdata['can_access_data'] = 'y';
        $this->session->userdata['can_access_logs'] = 'y';
        $this->session->userdata['can_admin_channels'] = 'y';
        $this->session->userdata['can_access_logs'] = 'y';
        $this->session->userdata['can_admin_upload_prefs'] = 'y';
        $this->session->userdata['can_access_logs'] = 'y';
        $this->session->userdata['can_admin_design'] = 'y';
        $this->session->userdata['can_access_logs'] = 'y';
        $this->session->userdata['can_admin_members'] = 'y';
        $this->session->userdata['can_delete_members'] = 'y';
        $this->session->userdata['can_admin_mbr_groups'] = 'y';
        $this->session->userdata['can_admin_mbr_templates'] = 'y';
        $this->session->userdata['can_ban_users'] = 'y';
        $this->session->userdata['can_admin_modules'] = 'y';
        $this->session->userdata['can_admin_templates'] = 'y';
        $this->session->userdata['can_edit_categories'] = 'y';
        $this->session->userdata['can_admin_modules'] = 'y';
        $this->session->userdata['can_delete_categories'] = 'y';
        $this->session->userdata['can_view_other_entries'] = 'y';
        $this->session->userdata['can_edit_other_entries'] = 'y';
        $this->session->userdata['can_assign_post_authors'] = 'y';
        $this->session->userdata['can_delete_self_entries'] = 'y';
        $this->session->userdata['can_delete_all_entries'] = 'y';
        $this->session->userdata['can_view_other_comments'] = 'y';
        $this->session->userdata['can_edit_own_comments'] = 'y';
        $this->session->userdata['can_delete_own_comments'] = 'y';
        $this->session->userdata['can_edit_all_comments'] = 'y';
        $this->session->userdata['can_delete_all_comments'] = 'y';
        $this->session->userdata['can_moderate_comments'] = 'y';
        $this->session->userdata['can_send_email'] = 'y';
        $this->session->userdata['can_send_cached_email'] = 'y';
        $this->session->userdata['can_email_member_groups'] = 'y';
        $this->session->userdata['can_email_mailinglist'] = 'y';
        $this->session->userdata['can_email_from_profile'] = 'y';
        $this->session->userdata['can_view_profiles'] = 'y';
        $this->session->userdata['can_edit_html_buttons'] = 'y';
        $this->session->userdata['can_post_comments'] = 'y';
        $this->session->userdata['exclude_from_moderation'] = 'y';
        $this->session->userdata['can_admin_modules'] = 'y';

        $this->router->set_class('cp');

        $this->load->library('cp');
    }
}

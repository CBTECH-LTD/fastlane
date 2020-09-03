<?php declare(strict_types = 1);

return [
    'add'                      => 'Add',
    'save'                     => 'Save',
    'new'                      => 'New',
    'settings'                 => 'Settings',
    'empty_lists'              => 'Nothing to show here.',
    'back_to_list'             => 'Back to list',
    'danger_zone'              => 'Danger Zone',
    'delete_entry_title'       => 'Delete this entry?',
    'delete_entry_description' => 'Once you delete an entry, there is no going back. Please be certain.',
    'delete_entry_button'      => 'Delete this entry',

    // Dashboard
    'dashboard'                => [
        'title' => 'Dashboard',
    ],

    // Content Entry Type
    'content'                  => [
        'singular_name' => 'Content',
        'plural_name'   => 'Contents',
        'identifier'    => 'contents',
    ],

    // File Manager Entry Type
    'file_manager'             => [
        'singular_name' => 'File',
        'plural_name'   => 'Files',
        'identifier'    => 'files',
    ],

    // Backend User Entry Type
    'user'                     => [
        'singular_name' => 'User',
        'plural_name'   => 'Users',
        'identifier'    => 'users',
    ],

    // Account Settings
    'account_settings'         => [
        'title'                               => 'Account Settings',
        'description'                         => 'Here you can setup your profile, change your password and manage API tokens.',
        'profile_menu'                        => 'Profile',
        'profile_title'                       => 'Edit account',
        'profile_success_msg'                 => 'Your account profile was updated successfully.',
        'security_menu'                       => 'Security',
        'security_title'                      => 'Edit your password',
        'security_new_password'               => 'New password',
        'security_confirm_new_password'       => 'Confirm new password',
        'personal_access_tokens_menu'         => 'Personal Access Tokens',
        'personal_access_tokens_title'        => 'Personal access tokens',
        'personal_access_tokens_new_button'   => 'Generate new token',
        'personal_access_tokens_revoke'       => 'Revoke',
        'personal_access_tokens_description'  => 'Personal access tokens can be used to communicate with the Fastlane Content API.',
        'personal_access_tokens_copy_message' => 'Make sure to copy your new personal access token now. You won’t be able to see it again!',
        'personal_access_tokens_last_used_at' => 'Last used at',
        'personal_access_tokens_empty'        => 'You haven\'t generated any personal access token.',
    ],

    // Control Panel Menu
    'menu'                     => [
        'system_group' => 'System',
    ],

    // Field labels
    'fields'                   => [
        'active'           => 'Active',
        'blocks'           => 'Blocks',
        'email'            => 'Email',
        'name'             => 'Name',
        'slug'             => 'Slug',
        'role'             => 'Role',
        'password'         => 'Password',
        'meta_title'       => 'Meta Title',
        'meta_description' => 'Meta Description',
        'file'             => [
            'selected' => 'No file selected|1 file selected|{count} files selected',
        ],
        'toggle'           => [
            'turn_on'  => 'Click to turn on',
            'turn_off' => 'Click to turn off',
        ],
    ],
];

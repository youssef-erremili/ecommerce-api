<?php

namespace App\Support;

class ApiMessages
{
    // Api actions
    const string SUCCESS = 'success';

    const string ERROR = 'error';

    const string NOTFOUND = 'notfound';

    const string AN_ERROR_OCCURRED = 'An unexpected error occurred';

    // User actions
    const string USER_FETCHED = 'User retrieved successfully';

    const string USER_CREATED = 'User created successfully';

    const string USER_UPDATED = 'User updated successfully';

    const string USER_DELETED = 'User deleted successfully';

    const string USER_LOGGED_IN = 'logged in successfully';

    const string USER_LOGGED_OUT = 'logged out successfully';

    // User errors
    const string USER_NOT_FOUND = 'User not found';

    const string USER_ALREADY_EXISTS = 'User already exists';
}

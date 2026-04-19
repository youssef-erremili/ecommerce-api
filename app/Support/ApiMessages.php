<?php

namespace App\Support;

class ApiMessages
{
    // Generic Statuses
    public const string SUCCESS = 'success';

    public const string ERROR = 'error';

    public const string NOT_FOUND = 'not_found';

    // Auth
    public const string AUTH_SUCCESSFUL_LOGIN = 'Login successful. Welcome back!';

    public const string AUTH_SUCCESSFUL_LOGOUT = 'Logout successful. See you soon!';

    public const string AUTH_SUCCESSFUL_REGISTRATION = 'Registration successful. Welcome to our platform!';

    public const string AUTH_FAILED_LOGIN = 'Invalid credentials. Please check your email and password.';

    public const string AUTH_UNAUTHORIZED = 'You are not authorized to perform this action.';

    public const string AUTH_UNAUTHENTICATED = 'Please log in to access this resource.';

    // User
    public const string USER_FETCHED = 'User profile retrieved successfully.';

    public const string USER_CREATED = 'User account created successfully.';

    public const string USER_UPDATED = 'User profile updated successfully.';

    public const string USER_DELETED = 'User account deleted successfully.';

    public const string USER_NOT_FOUND = 'The requested user could not be found.';

    public const string USER_ALREADY_EXISTS = 'A user with this email already exists.';

    // Product
    public const string PRODUCT_FETCHED = 'Product retrieved successfully.';

    public const string PRODUCT_CREATED = 'Product created successfully.';

    public const string PRODUCT_UPDATED = 'Product updated successfully.';

    public const string PRODUCT_DELETED = 'Product deleted successfully.';

    public const string PRODUCT_NOT_FOUND = 'The requested product could not be found.';

    public const string PRODUCT_CREATION_FAILED = 'Failed to create the product. Please try again.';

    public const string PRODUCT_UPDATE_FAILED = 'Failed to update the product. Please try again.';

    public const string PRODUCT_DELETION_FAILED = 'Failed to delete the product. Please try again.';

    public const string PRODUCT_UNAUTHORIZED_ACTION = 'You are not authorized to manage this product.';

    public const string USER_NOT_VENDOR = 'You must be a vendor to perform this action.';

    public const string ACCOUNT_ALREADY_VENDOR = 'This account is already registered as a vendor.';

    // General
    public const string AN_ERROR_OCCURRED = 'An unexpected error occurred. Please try again later.';

    public const string ACTION_COMPLETED = 'The request was processed successfully.';

    public const string ADMIN_ACTION_RESTRICTED = 'Administrators cannot perform this action.';

    public const string ADMIN_ACTION_AllOWED = 'Only administrators can perform this action.';

    // WishLists
    public const string WISH_ALREADY_EXISTS = 'A product is already exists.';

    public const string WISHLIST_EMPTY = 'A Wishlist is empty.';
}

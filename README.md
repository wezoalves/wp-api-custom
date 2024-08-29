# Recipes API Plugin

**Version:** 1.0.1  
**Author:** Weslley Alves  
**License:** GPL-2.0-or-later

## Description

The **Recipes API** plugin provides tools to manage and expose a custom "Recipes" Custom Post Type (CPT) via a REST API. The plugin adds custom meta boxes to the "Recipes" CPT, allowing you to specify which sites the entries will be available on. It also creates a REST API endpoint that returns the entries filtered based on selected sites, categories, and tags. The endpoint is protected via WordPress application passwords to ensure secure access.

## Features

- Adds a meta box to the "Recipes" CPT to select available sites.
- Creates an admin screen to manage the available sites.
- REST API endpoint to list "Recipes" CPT entries filtered based on selected sites, categories, and tags.
- Endpoint protection using WordPress application passwords.
- Supports filtering by multiple categories and tags with `AND` or `OR` relations.
- Supports pagination to control the number of results returned.

## Requirements

- WordPress 5.6 or later
- PHP 7.4 or later

## Installation

1. Clone or download this repository into your WordPress `wp-content/plugins/` folder.
2. Activate the plugin through the "Plugins" menu in WordPress.

## Usage

### 1. Managing Available Sites

- In the WordPress admin menu, go to **API Receitas**.
- Add, edit, or remove the available sites. These sites are used to select which sites the "Recipes" CPT entries will be available on.

### 2. Configuring the "Recipes" CPT

- When creating or editing a "Recipes" post, you will see a meta box where you can select the sites where the entry will be available.
- The sites available in the meta box are those configured in the "API Receitas" screen.

### 3. Consuming the REST API

The API endpoint is available at:

```
https://yoursite.com/api/v1/recipes
```

#### Parameters

- `site`: (optional) Filters the entries based on the selected site.
- `categories`: (optional) Filters entries by multiple categories. Accepts a comma-separated list of category IDs or slugs.
- `tags`: (optional) Filters entries by multiple tags. Accepts a comma-separated list of tag IDs or slugs.
- `relation`: (optional) Defines the relation between categories and tags, either `AND` or `OR`. The default is `AND`.
- `page`: (optional) The page number to return. Defaults to 1.
- `limit`: (optional) The number of items per page. Defaults to 10.
- `post_type`: (optional) Specify a custom post type to filter results. Defaults to the "Recipes" CPT.

Usage example:

```
https://yoursite.com/api/v1/recipes?site=sitea.com&categories=1,2&tags=breakfast,dinner&relation=OR&page=2&limit=5
```

This example will return page 2 of the results from `sitea.com` that are in either category `1` or `2` and have the tags `breakfast` or `dinner`, with 5 items per page.

### 4. Securing the Endpoint with Application Passwords

- To access the API, you must use basic authentication with a WordPress username and application password.

**Example Request:**

```bash
curl -X GET https://yoursite.com/api/v1/recipes?site=sitea.com \
-H "Authorization: Basic base64_encoded_string"
```

#### How to Generate an Application Password

- In the WordPress user profile, you will find the "Application Passwords" section.
- Create an application password and use it along with the username in the authentication.

### 5. Pagination Details

The API supports pagination using the `page` and `limit` parameters.

- `page`: Defines which page to return (defaults to 1).
- `limit`: Defines the number of items per page (defaults to 10).

The response includes the following pagination details:
- `total`: Total number of posts found.
- `total_pages`: Total number of pages available.
- `current_page`: The current page number.
- `limit`: The number of items returned per page.

## Changelog

### 1.0.1
- Added support for filtering recipes by tags.
- Updated the API to allow filtering by a combination of categories and tags with `AND` or `OR` relations.

## Author

- Weslley Alves - [LinkedIn](https://www.linkedin.com/in/wezoalves)
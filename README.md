
# Recipes API Plugin

**Version:** 1.0.0  
**Author:** Weslley Alves  
**License:** GPL-2.0-or-later

## Description

The **Recipes API** plugin allows adding a custom meta box to the "Recipes" Custom Post Type (CPT) to select which sites the entries will be available on. Additionally, the plugin creates a REST API endpoint that returns the entries filtered based on the selected sites and categories. Access to the endpoint is secured via WordPress application passwords.

## Features

- Adds a meta box to the "Recipes" CPT to select available sites.
- Creates an admin screen to manage the available sites.
- REST API endpoint to list "Recipes" CPT entries filtered based on selected sites and categories.
- Endpoint protection using WordPress application passwords.
- Supports filtering by multiple categories with `AND` or `OR` relation.
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
- `relation`: (optional) Defines the relation between categories, either `AND` or `OR`. The default is `AND`.
- `page`: (optional) The page number to return. Defaults to 1.
- `limit`: (optional) The number of items per page. Defaults to 10.

Usage example:

```
https://yoursite.com/api/v1/recipes?site=sitea.com&categories=1,2&relation=OR&page=2&limit=5
```

This example will return page 2 of the results from `sitea.com` that are in either category `1` or `2`, with 5 items per page.

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


## Author

- Weslley Alves - [LinkedIn](https://www.linkedin.com/in/wezoalves)

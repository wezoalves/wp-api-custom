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

## Requirements

- WordPress 5.6 or later
- PHP 7.4 or later

## Installation

1. Clone or download this repository into your WordPress `wp-content/plugins/` folder.
2. Activate the plugin through the "Plugins" menu in WordPress.

## Usage

### 1. Managing Available Sites

- In the WordPress admin menu, go to **Manage Sites**.
- Add, edit, or remove the available sites. These sites are used to select which sites the "Recipes" CPT entries will be available on.

### 2. Configuring the "Recipes" CPT

- When creating or editing a "Recipes" post, you will see a meta box where you can select the sites where the entry will be available.
- The sites available in the meta box are those configured in the "Manage Sites" screen.

### 3. Consuming the REST API

The API endpoint is available at:

```
https://yoursite.com/api/v1/recipes
```

#### Parameters

- `site`: (optional) Filters the entries based on the selected site.
- `categories`: (optional) Filters entries by multiple categories. Accepts a comma-separated list of category IDs or slugs.
- `relation`: (optional) Defines the relation between categories, either `AND` or `OR`. The default is `AND`.

Usage example:

```
https://yoursite.com/api/v1/recipes?site=sitea.com&categories=1,2&relation=OR
```

This example will return all recipes from `sitea.com` that are in either category `1` or `2`.

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

## Development

If you want to contribute to the development of this plugin:

1. Fork the repository.
2. Create a new branch for your changes: `git checkout -b my-feature`.
3. Commit your changes: `git commit -m 'Add my new feature'`.
4. Push your changes to the repository: `git push origin my-feature`.
5. Open a Pull Request.

## License

This plugin is distributed under the GPL-2.0-or-later license. For more information, see the LICENSE file.

## Author

- Weslley Alves - [LinkedIn](https://www.linkedin.com/in/wezoalves)
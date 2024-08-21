# Plugin Documentation

## 1. Overview

This document describes the installation and configuration of the **Recipes API** plugin, created to manage recipes via API in WordPress.

### Repository

- **GitHub Repository:** [https://github.com/wezoalves/wp-api-custom](https://github.com/wezoalves/wp-api-custom)
- **Plugin Download:** [Download Zip](https://github.com/wezoalves/wp-api-custom/archive/refs/heads/main.zip)

## 2. Installation

1. **Download the Zip**: Download the plugin using the link provided above.
2. **Install on WordPress**:
    - Go to the WordPress dashboard under **Plugins > Add New**.
    - Click on **Upload Plugin** and select the downloaded `.zip` file.
    - Activate the plugin after installation.

3. **Configure Permalinks**:
    - After activating the plugin, go to the **Permalinks** page at `/wp-admin/options-permalink.php` and click **Save** so WordPress recognizes the new routes added.

## 3. Plugin Settings

### Main File

The main file of the plugin is [recipes-api.php](https://github.com/wezoalves/wp-api-custom/blob/main/recipes-api.php). Lines 18 and 19 define the custom post type (CPT) and the custom fields that will be returned in the API's JSON object.

```php
// default
define('API_CUSTOM_CPTSLUG', 'recipes');
define('API_CUSTOM_FIELDS', ['yield', 'prep_time']);
```

### Endpoints

The main endpoint to fetch recipes is:

```
https://yoursite.com/api/v1/recipes
```

### WP-JSON Redirect

A redirect was set up from the default WordPress API (`wp-json`) to facilitate a future migration in case the API needs to be moved outside WordPress.

## 4. API Parameters

The API accepts the following parameters:

```
https://yoursite.com/api/v1/recipes?site=sitea&categories=1,2&relation=OR&page=1&limit=100
```

- **site**: Filters recipes by a specific site.
- **categories**: Filters recipes by specific categories (IDs).
- **relation**: Defines the relationship between category filters. Can be `OR` or `AND`. The default is `OR`.
- **page**: Defines the listing page. The default is `1`.
- **limit**: Defines the number of items per page. The default is `10`, with a maximum of `100`.

### Default Behavior

- If no parameters are provided, the API returns all available recipes.
- The default items per page are `10`, but the maximum allowed is `100`.
- The API supports pagination, with `page=1` as the default.

### Example Response

```json
{
  "query": {
    "post_type": "recipes",
    "posts_per_page": -1,
    "meta_query": [],
    "tax_query": []
  },
  "data": [
    {
      "id": 1,
      "title": "title recipe",
      "resume": "resume recipe excerpt based",
      "recipie_url": "https://site.com/page-recipie",
      "image_url": "https://site.com/page-recipie/image.png",
      "meta_yield": "Value Meta yield",
      "meta_prep_time": "Value Meta prep_time"
    }
    ...
  ],
  "status": 200
}
```

## 5. Administrative Interface

### Admin Menu

In the WordPress admin panel, a **Recipes API** menu will be added, where you can define:
- Available sites.
- Whether the API requires authentication or not.

![plugin](./screen-plugin.png)

### Recipe Editing

On the recipe editing screen, specific plugin fields appear at the bottom right.

![cpt](./screen-cpt.png)

---

For questions or support:

**Weslley Alves**  
Full Stack Developer  
+55 11 99897 0080  
weslley@tonica.ag
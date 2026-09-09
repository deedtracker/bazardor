# BazarDor API Documentation

Welcome to the BazarDor public API! Our API allows developers to programmatically access the latest retail market prices of groceries across Bangladesh.

This API is designed to be easily integrated into any mobile (Kotlin, Java, Flutter, Swift) or web application (React, Vue, Angular, Next.js). All endpoints return data in standard `JSON` format and support Cross-Origin Resource Sharing (CORS).

---

## Base URL
All API requests should be made relative to the base URL:
`https://bazardor.app/api/`

---

## 1. Get All Products

Retrieves a list of all available products, along with their current retail prices, yesterday's prices, and price trends. Also returns a list of available categories.

**Endpoint:** `GET /products.php`

### Query Parameters (Optional)
| Parameter  | Type     | Description                                  | Example            |
| :--------- | :------- | :------------------------------------------- | :----------------- |
| `q`        | `string` | Search products by Bengali or English name.  | `?q=আলু`           |
| `category` | `string` | Filter products by a specific category name. | `?category=সবজি` |

### Success Response (`200 OK`)
```json
{
  "success": true,
  "date": "2026-09-08",
  "categories": [
    {
      "id": 1,
      "name": "সবজি",
      "sort_order": 1
    },
    {
      "id": 2,
      "name": "মশলা",
      "sort_order": 2
    }
  ],
  "products": [
    {
      "id": 2,
      "slug": "potato-price-today",
      "name": "আলু",
      "name_en": "Potato",
      "category": "সবজি",
      "unit": "কেজি",
      "icon": "https://bazardor.app/assets/potato.png",
      "current_price": 22,
      "previous_price": 20,
      "diff": 2,
      "trend": "up",
      "last_updated": "2026-09-08"
    }
  ]
}
```

---

## 2. Get Single Product Details

Retrieves in-depth details for a specific product using its `slug` identifier. This includes historical data, future price predictions, related news, AI-generated summaries, and prices across different districts.

**Endpoint:** `GET /product.php`

### Query Parameters (Required)
| Parameter | Type     | Description                                 | Example                        |
| :-------- | :------- | :------------------------------------------ | :----------------------------- |
| `slug`    | `string` | The unique slug identifier for the product. | `?slug=potato-price-today`     |

### Success Response (`200 OK`)
```json
{
  "success": true,
  "product": {
    "id": 2,
    "slug": "potato-price-today",
    "name": "আলু",
    "name_en": "Potato",
    "category": "সবজি",
    "unit": "কেজি",
    "icon": "https://bazardor.app/assets/potato.png"
  },
  "pricing": {
    "current_price": 22,
    "previous_price": 20,
    "diff": 2,
    "trend": "up",
    "last_updated": "2026-09-08"
  },
  "city_prices": [
    {
      "city": "বগুড়া",
      "price": 15
    },
    {
      "city": "ঢাকা",
      "price": 20
    }
  ],
  "cheapest_city": "বগুড়া",
  "expensive_city": "রাঙ্গামাটি",
  "history": [
    {
      "date": "2026-09-07",
      "price": 20
    },
    {
      "date": "2026-09-08",
      "price": 22
    }
  ],
  "predictions": [
    {
      "date": "2026-09-09",
      "price": 21,
      "confidence": "medium"
    }
  ],
  "news": [
    {
      "title": "আলুর দাম স্থিতিশীল থাকবে বলে জানিয়েছে কৃষি মন্ত্রণালয়",
      "source": "যুগান্তর",
      "url": "https://example.com/news",
      "date": "2026-08-29"
    }
  ],
  "summary": "আজ ৮ সেপ্টেম্বর, ২০২৬। গতকালের চেয়ে আজ আলু এর দাম কেজিতে ২ টাকা বেড়েছে...",
  "trend_summary": "গত ৫ দিনে ওঠানামা করেছে, মোট ৩ টাকা কমেছে"
}
```

### Error Responses

**Condition:** Missing `slug` parameter
**Code:** `400 BAD REQUEST`
```json
{
  "success": false,
  "error": "slug parameter is required"
}
```

**Condition:** Product not found
**Code:** `404 NOT FOUND`
```json
{
  "success": false,
  "error": "Product not found"
}
```

---

## Integration Tips for Developers

*   **Null Safety:** Ensure your data models gracefully handle `null` values. For example, `url` inside a `news` item or `last_updated` inside a `product` may occasionally return `null`.
*   **JSON Parsing:** When setting up strong-typed classes (Java, Kotlin, Dart, Swift):
    *   `categories` in the `products.php` response is a **list of JSON objects** (containing `id`, `name`, `sort_order`), **not a list of strings**. 
    *   Price fields like `current_price`, `previous_price`, and `diff` should be parsed as floating-point numbers (`Double` or `Float`).
*   **Image Loading:** The `icon` field returns an absolute URL. Mobile developers should use caching image libraries (like `Coil`/`Glide` for Android, `Kingfisher` for iOS, and `cached_network_image` for Flutter) to load these efficiently.
*   **Missing Nodes:** Arrays like `city_prices`, `history`, `predictions`, and `news` may sometimes be empty `[]`. Design your UI so that it gracefully hides these sections if the array is empty instead of crashing or showing a blank card.

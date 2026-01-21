# QR Code API Documentation

## Overview
This API endpoint generates QR codes, replacing the Google Chart API. It works exactly like Google's Chart API but hosted on your own server. Uses the SimpleSoftwareIO QR Code package for Laravel.

## Endpoint

```
GET /api/qr-code
```

## Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| `data` | string | Yes | - | The data to encode in the QR code (max 1000 characters) |
| `size` | integer | No | `400` | Size in pixels (min: 100, max: 1000) |
| `format` | string | No | `png` | Output format: `png` or `svg` |

## Response Formats

### 1. PNG Format (Default)
Returns raw PNG image data with `Content-Type: image/png`

**Example Request:**
```bash
curl "http://your-domain.com/api/qr-code?data=https://example.com" --output qrcode.png
```

**Response:**
Raw PNG image binary data

### 2. SVG Format
Returns raw SVG image data with `Content-Type: image/svg+xml`

**Example Request:**
```bash
curl "http://your-domain.com/api/qr-code?data=https://example.com&format=svg"
```

**Response:**
```xml
<svg xmlns="http://www.w3.org/2000/svg" ...>
  <!-- SVG content -->
</svg>
```

## Examples

### Basic QR Code
```bash
curl "http://your-domain.com/api/qr-code?data=Hello%20World"
```

### Custom Size
```bash
curl "http://your-domain.com/api/qr-code?data=Hello%20World&size=600"
```

### SVG Format
```bash
curl "http://your-domain.com/api/qr-code?data=https://example.com&format=svg"
```

## Error Responses

### Validation Error
```json
{
  "status_code": 422,
  "message": "The given data was invalid.",
  "errors": {
    "data": ["QR code data is required"],
    "size": ["Size must be at least 100 pixels"]
  }
}
```

### Server Error
```json
{
  "status_code": 500,
  "message": "Error generating QR code: [error details]"
}
```

## Usage in HTML

### Display QR Code (Simple as Google Chart API)
```html
<img src="http://your-domain.com/api/qr-code?data=https://example.com&size=300" alt="QR Code">
```

### Display SVG QR Code
```html
<img src="http://your-domain.com/api/qr-code?data=https://example.com&format=svg" alt="QR Code">
```

## Migration from Google Chart API

**Old Google Chart API:**
```
https://chart.googleapis.com/chart?cht=qr&chl=YOUR_DATA&chs=400x400
```

**New Laravel API (Drop-in Replacement):**
```
http://your-domain.com/api/qr-code?data=YOUR_DATA&size=400
```

Simply replace the domain and use the new parameter names!

## Validation Rules

- **data**: Required, string, maximum 1000 characters
- **size**: Optional, integer between 100 and 1000 pixels
- **format**: Optional, must be one of: `png` (default), `svg`

## Rate Limiting

The API uses Laravel's default rate limiting. Check your application's rate limit configuration.

## Features

- **Fixed Styling**: Consistent black and white QR codes (like Google Chart API)
- **High Error Correction**: Level H error correction for better readability
- **UTF-8 Support**: Properly handles international characters
- **Caching**: Responses include cache headers for better performance
- **Fast Generation**: Generates QR codes on-the-fly with minimal overhead

## Security Notes

- Input data is validated and sanitized
- Maximum data length is enforced to prevent abuse
- Size limits prevent resource exhaustion
- UTF-8 encoding is enforced for proper character support

## Performance Tips

1. **PNG format** (default) is best for most use cases
2. **SVG format** is great for web display (smaller file size, scalable)
3. Cache QR codes on the client side (responses include cache headers)
4. Use appropriate size values (larger sizes increase generation time)

## Support

For issues or questions, please contact your development team.

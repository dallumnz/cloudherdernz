# CloudHerder Deployment Notes

## Media File Caching (Self-Hosted Audio/Video)

Self-hosted media files are stored via Spatie MediaLibrary on the `public` disk
(`storage/app/public`) and served through the `public/storage` symlink.

### nginx Configuration

Add a location block to cache media files aggressively. This belongs in the
production server block for `cloudherder.nz`:

```nginx
# Cache media files from MediaLibrary
location /storage/media/ {
    expires 1y;
    add_header Cache-Control "public, immutable";
    add_header Vary "Accept-Encoding";
}
```

### PHP Upload Limits

Ensure PHP can accept files up to the MediaLibrary limit (currently 500MB):

```ini
; php.ini
upload_max_filesize = 512M
post_max_size = 512M
max_execution_time = 300
memory_limit = 512M
```

Restart PHP-FPM after changes.

### Storage Link

Ensure the storage symlink exists on production:

```bash
php artisan storage:link
```

### S3 Migration Path

When local disk becomes a bottleneck, configure the `s3` disk in
`config/filesystems.php` and update `MEDIA_DISK` in `.env`:

```env
MEDIA_DISK=s3
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=...
AWS_BUCKET=...
AWS_URL=https://cdn.cloudherder.nz
```

No code changes required — MediaLibrary respects the `disk_name` config.

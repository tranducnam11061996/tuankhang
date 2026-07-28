# Thiết kế chuyển luồng sản phẩm sang Tailwind CSS

## Mục tiêu

Chuyển frontend của archive `san-pham`, taxonomy `danh-muc`, tìm kiếm sản phẩm và single product sang Tailwind CSS v4, giữ nguyên URL, dữ liệu, nội dung, màu sắc và bố cục desktop. Các trang ngoài phạm vi tiếp tục dùng giao diện legacy.

## Kiến trúc

- Tách một shared Tailwind shell cho header, footer, menu, dropdown, map và Facebook loader; trang chủ và luồng sản phẩm chỉ nạp thêm bundle riêng.
- Asset production gồm `site.min.css/js`, `home.min.css/js` và `products.min.css/js`, được enqueue theo context và version bằng `filemtime`.
- Không tải jQuery, UIkit, Owl, Slick, Flexslider, Font Awesome hoặc stylesheet legacy trong Tailwind context. Contact Form 7 JavaScript chỉ được giữ trên single product.
- Archive, taxonomy và product search dùng chung breadcrumb, sidebar, product card và pagination. Single product dùng component card chung cho sản phẩm liên quan.

## Giao diện

- Desktop listing giữ sidebar 1/4 và product grid 4 cột. Mobile dùng 1 cột dưới 480px, 2 cột từ 480px và drawer danh mục accessible.
- Single product giữ tỷ lệ 40/60, nội dung chi tiết responsive và 4 sản phẩm liên quan. Mobile dùng thanh sticky 56px cho Đăng ký/Hotline và modal accessible cho Contact Form 7 ID 14.
- Banner được render bằng `<picture>` trong HTML, có overlay và tỷ lệ tương đương thiết kế cũ.

## Hình ảnh và hiệu năng

- Sinh AVIF/WebP cho banner 480/768/1200/1702px, card 320/480/768px và ảnh chính 480/768/1024px; luôn fallback ảnh WordPress.
- Ảnh có kích thước, `srcset`, `sizes`, `decoding` và chiến lược loading phù hợp. Map và Facebook được trì hoãn.
- Mục tiêu archive: CSS không quá 45KB raw, theme JavaScript không quá 12KB raw, khoảng tối đa 20 request initial, LCP mobile dưới 2,5 giây và CLS dưới 0,1.

## Tương thích và kiểm thử

- Không thay đổi database, ID, taxonomy relationship, permalink hoặc meta key.
- Test PHP lint, build idempotent, VI/EN, keyboard, modal/CF7, responsive 320–1440px, content media, empty state và legacy smoke tests.
- Xác nhận vẫn có 17 sản phẩm, 21 term; không còn warning/fatal/HTTP 500 và không còn request legacy trong Tailwind context.

<?php
/**
 * Theme Options schema and defaults.
 */

if (!defined('ABSPATH')) {
    exit;
}

function tk_theme_site_defaults()
{
    return array(
        'brand' => array(
            'company_name' => 'Công ty TNHH Dược và Thiết bị y tế Tuấn Khang',
            'logo_id' => 0,
            'logo_white_id' => 0,
        ),
        'contact' => array(
            'hotline' => '',
            'landline' => '',
            'email' => '',
            'consultation_url' => home_url('/lien-he/'),
        ),
        'branches' => array(
            array('label' => 'Hà Nội', 'address' => 'Số 23, ngõ 38 Phương Mai, Phường Kim Liên, Hà Nội.'),
            array('label' => 'Hồ Chí Minh', 'address' => 'Số 1/1 Đường Hoàng Việt, Phường Tân Sơn Nhất, Hồ Chí Minh.'),
        ),
        'map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.685746165307!2d105.83404037627878!3d21.005230488584314!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ac7919c6b95b%3A0x624a289a6ba5cdc!2zMjMgTmcuIDM4IFAuIFBoxrDGoW5nIE1haSwgS2ltIExpw6puLCDEkOG7kW5nIMSQYSwgSMOgIE7hu5lpLCBWaeG7h3QgTmFt!5e0!3m2!1svi!2s!4v1734943915820!5m2!1svi!2s',
        'social' => array(
            'zalo_url' => '',
            'messenger_url' => 'https://m.me/108890274790969',
            'facebook_url' => '',
            'instagram_url' => '',
            'youtube_url' => '',
            'tiktok_url' => '',
            'linkedin_url' => '',
            'x_url' => '',
        ),
        'integrations' => array(
            'contact_form_id' => 14,
            'certificate_image_id' => 0,
            'certificate_url' => 'http://online.gov.vn/Home/WebDetails/84000',
        ),
    );
}

function tk_theme_home_defaults()
{
    return array(
        'hero' => array(
            'eyebrow' => 'Hơn một thập kỷ đồng hành cùng bác sĩ nha khoa',
            'title' => 'Nâng chuẩn điều trị nha khoa Việt Nam',
            'description' => 'Tuấn Khang kết nối bác sĩ và phòng khám với thiết bị, vật liệu và hệ thống Implant được tuyển chọn từ những thương hiệu uy tín trên thế giới.',
            'image_id' => 0,
            'secondary_image_id' => 2250,
            'primary_label' => 'Nhận tư vấn chuyên môn',
            'primary_url' => home_url('/lien-he/'),
            'secondary_label' => 'Khám phá sản phẩm',
            'secondary_url' => home_url('/san-pham/'),
        ),
        'metrics' => array(
            array('value' => '100000', 'suffix' => '+', 'label' => 'Khách hàng'),
            array('value' => '30', 'suffix' => '+', 'label' => 'Hãng sản xuất'),
            array('value' => '35', 'suffix' => '+', 'label' => 'Container mỗi năm'),
            array('value' => '24', 'suffix' => '/7', 'label' => 'Hỗ trợ chuyên môn'),
            array('value' => '12', 'suffix' => '+', 'label' => 'Năm kinh nghiệm'),
        ),
        'partners_heading' => array(
            'eyebrow' => 'Mạng lưới quốc tế',
            'title' => 'Đối tác đồng hành',
        ),
        'partners' => array(),
        'story' => array(
            'eyebrow' => 'Câu chuyện thương hiệu',
            'title' => 'Câu chuyện về Tuấn Khang',
            'content' => '',
            'image_id' => 0,
            'url' => home_url('/gioi-thieu/'),
        ),
        'values_heading' => array(
            'eyebrow' => 'Giá trị dẫn đường',
            'title' => 'Mục tiêu · Sứ mệnh · Tầm nhìn',
            'description' => 'Ba cam kết định hướng cách Tuấn Khang lựa chọn sản phẩm, phục vụ khách hàng và phát triển dài hạn.',
        ),
        'values' => array(
            array('title' => 'Mục Tiêu', 'description' => '', 'icon' => 'target'),
            array('title' => 'Sứ Mệnh', 'description' => '', 'icon' => 'mission'),
            array('title' => 'Tầm Nhìn', 'description' => '', 'icon' => 'vision'),
        ),
        'capability' => array(
            'eyebrow' => 'Nền tảng vận hành',
            'title' => 'Năng lực tạo nên khác biệt',
            'description' => 'Tuấn Khang kết hợp danh mục sản phẩm được tuyển chọn, hỗ trợ chuyên môn và năng lực phân phối để đồng hành lâu dài cùng bác sĩ.',
            'image_id' => 0,
            'items' => array(
                array('title' => 'Danh mục được tuyển chọn', 'description' => 'Thiết bị, vật liệu và hệ thống Implant chính hãng từ các nhà sản xuất đã được kiểm chứng.', 'url' => home_url('/san-pham/'), 'icon' => 'portfolio'),
                array('title' => 'Hỗ trợ lâm sàng', 'description' => 'Đồng hành cùng bác sĩ từ lựa chọn giải pháp đến hướng dẫn sử dụng và hỗ trợ chuyên môn.', 'url' => home_url('/dich-vu-ho-tro-phau-thuat/'), 'icon' => 'clinical'),
                array('title' => 'Phân phối toàn quốc', 'description' => 'Năng lực cung ứng ổn định cùng hệ thống chi nhánh tại Hà Nội và Thành phố Hồ Chí Minh.', 'url' => home_url('/lien-he/'), 'icon' => 'distribution'),
            ),
        ),
        'projects_heading' => array(
            'eyebrow' => 'Năng lực triển khai',
            'title' => 'Dự án tiêu biểu',
            'description' => 'Kinh nghiệm cung cấp giải pháp cho bệnh viện, phòng khám và các đơn vị y tế.',
        ),
        'projects' => array(
            array('slug' => 'project-hanam', 'image_id' => 0, 'eyebrow' => 'Công trình trọng điểm', 'title' => 'Bệnh viện tỉnh Hà Nam', 'description' => 'Giải pháp thiết bị y tế được tuyển chọn cho môi trường điều trị hiện đại.'),
            array('slug' => 'project-dany', 'image_id' => 0, 'eyebrow' => 'Dự án triển khai', 'title' => 'Bệnh viện Dân Y', 'description' => 'Đồng hành kiến tạo hạ tầng chăm sóc sức khỏe.'),
            array('slug' => 'project-phusan', 'image_id' => 0, 'eyebrow' => 'Không gian chuyên sâu', 'title' => 'Bệnh viện Phụ sản Trung ương', 'description' => 'Thiết bị phù hợp yêu cầu vận hành lâm sàng.'),
            array('slug' => 'project-thucuc', 'image_id' => 0, 'eyebrow' => 'Môi trường điều trị', 'title' => 'Bệnh viện Thu Cúc', 'description' => 'Không gian chăm sóc an toàn, hiệu quả.'),
            array('slug' => 'project-bichngoc', 'image_id' => 0, 'eyebrow' => 'Chăm sóc chuyên biệt', 'title' => 'Bệnh viện Bích Ngọc, Nam Định', 'description' => 'Giải pháp thiết bị cho khu vực chuyên sâu.'),
        ),
        'final_cta' => array(
            'title' => 'Cùng Tuấn Khang lựa chọn giải pháp phù hợp cho phòng khám',
            'description' => 'Đội ngũ chuyên môn sẵn sàng tư vấn về hệ thống Implant, thiết bị và vật liệu nha khoa.',
            'label' => 'Nhận tư vấn chuyên môn',
            'url' => home_url('/lien-he/'),
        ),
    );
}

function tk_theme_option_name($scope)
{
    return $scope === 'home' ? 'tuankhang_home_options' : 'tuankhang_site_options';
}

function tk_theme_array_replace_recursive($defaults, $values)
{
    if (!is_array($defaults) || !is_array($values)) {
        return $values;
    }

    // Repeaters are complete user-managed lists. Replacing a list as a whole
    // preserves an intentional empty value instead of restoring defaults.
    if (tk_theme_array_is_list($defaults) || tk_theme_array_is_list($values)) {
        return $values;
    }

    foreach ($values as $key => $value) {
        if (array_key_exists($key, $defaults) && is_array($defaults[$key]) && is_array($value)) {
            $defaults[$key] = tk_theme_array_replace_recursive($defaults[$key], $value);
        } else {
            $defaults[$key] = $value;
        }
    }

    return $defaults;
}

function tk_theme_array_is_list($value)
{
    if (!is_array($value)) {
        return false;
    }
    if ($value === array()) {
        return true;
    }
    return array_keys($value) === range(0, count($value) - 1);
}

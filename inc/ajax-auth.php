<?php
/**
 * AJAX Login and Registration
 *
 * @package CustomShop
 */

if (!defined('ABSPATH')) exit;

/**
 * AJAX Login
 */
add_action('wp_ajax_nopriv_ajax_login', 'customshop_ajax_login');
function customshop_ajax_login() {
    // Проверяем nonce
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'ajax-login-nonce')) {
        wp_send_json_error('Ошибка безопасности. Обновите страницу и попробуйте снова.');
        return;
    }

    // Получаем данные
    $username = isset($_POST['username']) ? sanitize_text_field($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['rememberme']) ? true : false;

    // Валидация
    if (empty($username) || empty($password)) {
        wp_send_json_error('Заполните все обязательные поля.');
        return;
    }

    // Данные для входа
    $creds = array(
        'user_login'    => $username,
        'user_password' => $password,
        'remember'      => $remember
    );

    // Попытка входа
    $user = wp_signon($creds, false);

    if (is_wp_error($user)) {
        // Переводим стандартные сообщения об ошибках
        $error_message = $user->get_error_message();

        if (strpos($error_message, 'incorrect') !== false || strpos($error_message, 'Invalid') !== false) {
            $error_message = 'Неверный логин или пароль.';
        }

        wp_send_json_error($error_message);
        return;
    }

    wp_send_json_success(array('message' => 'Вы успешно вошли в систему!'));
}

/**
 * AJAX Register
 */
add_action('wp_ajax_nopriv_ajax_register', 'customshop_ajax_register');
function customshop_ajax_register() {
    // Проверяем nonce
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'ajax-register-nonce')) {
        wp_send_json_error('Ошибка безопасности. Обновите страницу и попробуйте снова.');
        return;
    }

    // Проверяем, что регистрация включена
    if (!get_option('users_can_register')) {
        wp_send_json_error('Регистрация новых пользователей отключена.');
        return;
    }

    // Получаем и очищаем данные
    $username = isset($_POST['username']) ? sanitize_user($_POST['username']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Валидация
    if (empty($username) || empty($email) || empty($password)) {
        wp_send_json_error('Заполните все обязательные поля.');
        return;
    }

    if (strlen($username) < 3) {
        wp_send_json_error('Логин должен содержать минимум 3 символа.');
        return;
    }

    if (strlen($password) < 6) {
        wp_send_json_error('Пароль должен содержать минимум 6 символов.');
        return;
    }

    if (!is_email($email)) {
        wp_send_json_error('Неверный формат email.');
        return;
    }

    if (username_exists($username)) {
        wp_send_json_error('Пользователь с таким логином уже существует.');
        return;
    }

    if (email_exists($email)) {
        wp_send_json_error('Пользователь с таким email уже зарегистрирован.');
        return;
    }

    // Создаём пользователя
    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        wp_send_json_error('Ошибка регистрации: ' . $user_id->get_error_message());
        return;
    }

    // Автоматический вход после регистрации
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);

    // Отправляем приветственное письмо (опционально)
    wp_new_user_notification($user_id, null, 'user');

    wp_send_json_success(array('message' => 'Регистрация прошла успешно! Вы автоматически вошли в систему.'));
}

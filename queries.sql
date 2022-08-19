/* CREATE TABLE LOG_ORDERS */
CREATE TABLE `log_orders` (
    `auto_id` bigint AUTO_INCREMENT PRIMARY KEY,
    `id` int(10) UNSIGNED NULL,
    `user_id` int(10) UNSIGNED NULL,
    `order_status_id` int(10) UNSIGNED NULL,
    `tax` double(5, 2),
    `delivery_fee` double(5, 2),
    `hint` text NULL,
    `active` tinyint(1) NULL,
    `driver_id` int(10) UNSIGNED NULL,
    `delivery_address_id` int(10) UNSIGNED NULL,
    `payment_id` int(10) UNSIGNED NULL,
    `created_at` timestamp NULL,
    `updated_at` timestamp NULL,
    `settlement_driver_id` int(10) UNSIGNED NULL,
    `settlement_manager_id` int(10) UNSIGNED NULL,
    `unregistered_customer_id` bigint(20) UNSIGNED NULL,
    `restaurant_delivery_fee` double(5, 2),
    `delivery_coupon_id` int(10) UNSIGNED NULL,
    `delivery_coupon_value` double(5, 2),
    `restaurant_coupon_id` int(10) UNSIGNED NULL,
    `restaurant_coupon_value` double(5, 2),
    `restaurant_id` int(10) UNSIGNED NULL,
    `for_restaurants` tinyint(1) NULL,
    `reason` text NULL,
    `processing_time` int(11) NULL,
    `operation` text,
    `action_time` timestamp DEFAULT now(),
    `last_user_action` int(10) UNSIGNED
);

/* INSERT ORDER TRIGGER */
CREATE TRIGGER log_orders_insert
AFTER
INSERT
    ON orders FOR EACH ROW
INSERT INTO
    `log_orders`(
        `id`,
        `user_id`,
        `order_status_id`,
        `tax`,
        `delivery_fee`,
        `hint`,
        `active`,
        `driver_id`,
        `delivery_address_id`,
        `payment_id`,
        `created_at`,
        `updated_at`,
        `settlement_driver_id`,
        `settlement_manager_id`,
        `unregistered_customer_id`,
        `restaurant_delivery_fee`,
        `delivery_coupon_id`,
        `delivery_coupon_value`,
        `restaurant_coupon_id`,
        `restaurant_coupon_value`,
        `restaurant_id`,
        `for_restaurants`,
        `reason`,
        `processing_time`,
        `operation`,
        `action_time`,
        `last_user_action`
    )
VALUES
    (
        NEW.`id`,
        NEW.`user_id`,
        NEW.`order_status_id`,
        NEW.`tax`,
        NEW.`delivery_fee`,
        NEW.`hint`,
        NEW.`active`,
        NEW.`driver_id`,
        NEW.`delivery_address_id`,
        NEW.`payment_id`,
        NEW.`created_at`,
        NEW.`updated_at`,
        NEW.`settlement_driver_id`,
        NEW.`settlement_manager_id`,
        NEW.`unregistered_customer_id`,
        NEW.`restaurant_delivery_fee`,
        NEW.`delivery_coupon_id`,
        NEW.`delivery_coupon_value`,
        NEW.`restaurant_coupon_id`,
        NEW.`restaurant_coupon_value`,
        NEW.`restaurant_id`,
        NEW.`for_restaurants`,
        NEW.`reason`,
        NEW.`processing_time`,
        'INSERT',
        NOW(),
        NEW.`last_user_action`
    );

/* UPDATE ORDER TRIGGER */
CREATE TRIGGER log_orders_update
AFTER
UPDATE
    ON orders FOR EACH ROW
INSERT INTO
    `log_orders`(
        `id`,
        `user_id`,
        `order_status_id`,
        `tax`,
        `delivery_fee`,
        `hint`,
        `active`,
        `driver_id`,
        `delivery_address_id`,
        `payment_id`,
        `created_at`,
        `updated_at`,
        `settlement_driver_id`,
        `settlement_manager_id`,
        `unregistered_customer_id`,
        `restaurant_delivery_fee`,
        `delivery_coupon_id`,
        `delivery_coupon_value`,
        `restaurant_coupon_id`,
        `restaurant_coupon_value`,
        `restaurant_id`,
        `for_restaurants`,
        `reason`,
        `processing_time`,
        `operation`,
        `action_time`,
        `last_user_action`
    )
VALUES
    (
        NEW.`id`,
        NEW.`user_id`,
        NEW.`order_status_id`,
        NEW.`tax`,
        NEW.`delivery_fee`,
        NEW.`hint`,
        NEW.`active`,
        NEW.`driver_id`,
        NEW.`delivery_address_id`,
        NEW.`payment_id`,
        NEW.`created_at`,
        NEW.`updated_at`,
        NEW.`settlement_driver_id`,
        NEW.`settlement_manager_id`,
        NEW.`unregistered_customer_id`,
        NEW.`restaurant_delivery_fee`,
        NEW.`delivery_coupon_id`,
        NEW.`delivery_coupon_value`,
        NEW.`restaurant_coupon_id`,
        NEW.`restaurant_coupon_value`,
        NEW.`restaurant_id`,
        NEW.`for_restaurants`,
        NEW.`reason`,
        NEW.`processing_time`,
        'UPDATE',
        NOW(),
        NEW.`last_user_action`
    );

/* DELTE ORDER TRIGGER */
CREATE TRIGGER log_orders_delete
AFTER
    DELETE ON orders FOR EACH ROW
INSERT INTO
    `log_orders`(
        `id`,
        `user_id`,
        `order_status_id`,
        `tax`,
        `delivery_fee`,
        `hint`,
        `active`,
        `driver_id`,
        `delivery_address_id`,
        `payment_id`,
        `created_at`,
        `updated_at`,
        `settlement_driver_id`,
        `settlement_manager_id`,
        `unregistered_customer_id`,
        `restaurant_delivery_fee`,
        `delivery_coupon_id`,
        `delivery_coupon_value`,
        `restaurant_coupon_id`,
        `restaurant_coupon_value`,
        `restaurant_id`,
        `for_restaurants`,
        `reason`,
        `processing_time`,
        `operation`,
        `action_time`,
        `last_user_action`
    )
VALUES
    (
        OLD.`id`,
        OLD.`user_id`,
        OLD.`order_status_id`,
        OLD.`tax`,
        OLD.`delivery_fee`,
        OLD.`hint`,
        OLD.`active`,
        OLD.`driver_id`,
        OLD.`delivery_address_id`,
        OLD.`payment_id`,
        OLD.`created_at`,
        OLD.`updated_at`,
        OLD.`settlement_driver_id`,
        OLD.`settlement_manager_id`,
        OLD.`unregistered_customer_id`,
        OLD.`restaurant_delivery_fee`,
        OLD.`delivery_coupon_id`,
        OLD.`delivery_coupon_value`,
        OLD.`restaurant_coupon_id`,
        OLD.`restaurant_coupon_value`,
        OLD.`restaurant_id`,
        OLD.`for_restaurants`,
        OLD.`reason`,
        OLD.`processing_time`,
        'DELETE',
        NOW(),
        OLD.`last_user_action`
    );
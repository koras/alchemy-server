https://yookassa.ru/developers/payment-acceptance/integration-scenarios/mobile-sdks/payments-with-tokens



Потом ограничить
https://yookassa.ru/developers/using-api/webhooks

185.71.76.0/27
185.71.77.0/27
77.75.153.0/25
77.75.156.11
77.75.156.35
77.75.154.128/25
2a02:5180::/32



https://www.google.com/imgres?q=yookassa%20api%20%D1%81%D1%85%D0%B5%D0%BC%D0%B0%20%D0%BE%D0%BF%D0%BB%D0%B0%D1%82%D0%B0&imgurl=https%3A%2F%2Fstatic.yoomoney.ru%2Fcheckout-docs-portal%2Farticles-public%2Fdocs-payment-solution-schema-receipt-after.image.ru.svg&imgrefurl=https%3A%2F%2Fyookassa.ru%2Fdocs%2Fpayment-solution%2Fpayments%2Fpayment-process%2Fdetails%2Freceipt&docid=RmsHV3kJaSRQEM&tbnid=cWFWKQzD23nx8M&vet=12ahUKEwiDy6DquOWNAxWXJhAIHavYIPwQM3oECGoQAA..i&w=1400&h=1260&hcb=2&ved=2ahUKEwiDy6DquOWNAxWXJhAIHavYIPwQM3oECGoQAA


https://yookassa.ru/developers/payment-acceptance/scenario-extensions/invoices/payments


CREATE TABLE `feedback` (
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `level_id` INT(10) NULL DEFAULT NULL,
    `user_id` INT(10) NULL DEFAULT NULL,
    `email` VARCHAR(128) NULL DEFAULT NULL COLLATE 'utf16_bin',
    `message` TEXT(32767) NULL DEFAULT NULL COLLATE 'utf16_bin',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`) USING BTREE
)
    COLLATE='utf16_bin'
    ENGINE=InnoDB;

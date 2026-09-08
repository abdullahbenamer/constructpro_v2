<?php

FlashHelper::error(
    'You do not have permission to access this page'
);

header('Location: ' . URLROOT . '/errors/forbidden');
exit;

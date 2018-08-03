<?php
date_default_timezone_set('UTC');

const ENVIRONMENT = 'dev'; // 'dev' or 'prod'
const VIEW_ENGINE = 'blade'; // 'blade' or 'php'
const GZIP_ENABLED = true; // can also be set on web server level
const PRETTY_ERROR_PAGES = true;
const PROJECT_ROOT_ABS_PATH = '/path/to/project/root/'; // full path to project root without trailing slash
const MONGO_DB_ENABLED = true;
const MONGO_DB_HOST = 'localhost';
const MONGO_DB_PORT = '27017'; // mongodb default: 27017
const MONGO_DB_USERNAME = '';
const MONGO_DB_PASSWORD = '';
const MONGO_DB_DEFAULT_DATABASE_NAME = 'veezee';

const SITE_TITLE = 'veezee';

const MAIN_DOMAIN = "192.168.0.23";
const MAIN_DOMAIN_PORT = ":8181";
const SITE_ADDRESS = 'http://' . MAIN_DOMAIN . MAIN_DOMAIN_PORT;

const BASE_IMAGES_URL = SITE_ADDRESS . "/content/images";
const BASE_MUSICS_URL = SITE_ADDRESS . "/content/music/albums";

const ADMIN_URL = 'admin';
const USERS_API_ENABLED = true;

const JWT_SECRET = 'whateveryouwant';

// for openssl - probabely no need to change this
const USE_CUSTOM_OPEN_SSL_CERT = false;
const VERIFY_CUSTOM_OPEN_SSL_CERT = false;
const CUSTOM_OPEN_SSL_CERT_PATH = null;
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5, user-scalable=1" name="viewport"/>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts-->
    <link href="https://fonts.googleapis.com/css?family={{ urlencode(theme_option('primary_font', 'Nunito Sans')) }}:300,600,700,800" rel="stylesheet" type="text/css">
    <!-- CSS Library-->

    <style>
        :root {
            --primary-color: {{ theme_option('primary_color', '#1d5f6f') }};
            --primary-color-rgb: {{ hex_to_rgba(theme_option('primary_color', '#1d5f6f'), 0.8) }};
            --primary-color-hover: {{ theme_option('primary_color_hover', '#063a5d') }};
            --primary-font: '{{ theme_option('primary_font', 'Nunito Sans') }}';
        }
        
        /* Estilos para o banner de vídeo */
        .home_banner.video-banner {
            position: relative;
            overflow: hidden;
        }

        #home-background-video {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: 0;
            transform: translateX(-50%) translateY(-50%);
            object-fit: cover;
        }

        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            z-index: 1;
        }

        .video-banner .topsearch {
            position: relative;
            z-index: 2;
        }
    </style>

    {!! Theme::header() !!}
</head>
<body @if (BaseHelper::siteLanguageDirection() == 'rtl') dir="rtl" @endif>
{!! apply_filters(THEME_FRONT_BODY, null) !!}
<div id="alert-container"></div>
<div class="bravo_topbar d-none d-sm-block">
    <div class="container-fluid w90">
        <div class="row">
            <div class="col-12">
                <div class="content">
                    <div class="topbar-left">
                        @if (theme_option('social_links'))
                            <div class="top-socials">
                                @foreach(json_decode(theme_option('social_links'), true) as $socialLink)
                                    @if (count($socialLink) == 3)
                                        <a href="{{ $socialLink[2]['value'] }}"
                                           title="{{ $socialLink[0]['value'] }}">
                                            <i class="{{ $socialLink[1]['value'] }}"></i>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                            <span class="line"></span>
                        @endif
                        <a href="mailto:{{ theme_option('email') }}">{{ theme_option('email') }}</a>
                    </div>
                    <div class="topbar-right">
                        @if (is_plugin_active('real-estate'))
                            <ul class="topbar-items">
                                <li><a href="{{ route('public.wishlist') }}"><i class="fas fa-heart"></i> {{ __('Wishlist') }}(<span class="wishlist-count">0</span>)</a></li>
                            </ul>
                            @php $currencies = get_all_currencies(); @endphp
                            @if (count($currencies) > 1)
                                <div class="language currency-switcher">
                                    <div class="language-switcher-wrapper">
                                        <div class="dropdown d-inline-block">
                                            <button class="btn btn-secondary dropdown-toggle btn-select-language" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                {{ get_application_currency()->title }}
                                            </button>
                                            <ul class="dropdown-menu language_bar_chooser">
                                                @foreach ($currencies as $currency)
                                                    <li>
                                                        <a href="{{ route('public.change-currency', $currency->title) }}" @if (get_application_currency_id() == $currency->id) class="active" @endif><span>{{ $currency->title }}</span></a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                        {!! Theme::partial('language-switcher') !!}
                        @if (is_plugin_active('real-estate') && RealEstateHelper::isRegisterEnabled())
                            <ul class="topbar-items">
                                @if (auth('account')->check())
                                    <li class="login-item"><a href="{{ route('public.account.dashboard') }}" rel="nofollow"><i class="fas fa-user"></i> <span>{{ auth('account')->user()->name }}</span></a></li>
                                    <li class="login-item"><a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" rel="nofollow"><i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}</a></li>
                                @else
                                    <li class="login-item">
                                        <a href="{{ route('public.account.login') }}"><i class="fas fa-sign-in-alt"></i>  {{ __('Login') }}</a>
                                    </li>
                                @endif
                            </ul>
                            @if (auth('account')->check())
                                <form id="logout-form" action="{{ route('public.account.logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<header class="topmenu bg-light">
    <div @if (theme_option('enable_sticky_header', 'yes') == 'yes') id="header-waypoint" @endif class="main-header">
        <div class="container-fluid w90">
            <div class="row">
                <div class="col-12">
                    <nav class="navbar navbar-expand-lg navbar-light">
                        @if (theme_option('logo'))
                            <a class="navbar-brand" href="{{ route('public.single') }}">
                                <img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}"
                                     class="logo" height="40" alt="{{ theme_option('site_name') }}">
                            </a>
                        @endif
                        <button class="navbar-toggler" type="button" data-toggle="collapse"
                                id="header-waypoint"                   data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                                aria-expanded="false" aria-label="Toggle navigation">
                            <span class="fas fa-bars"></span>
                        </button>

                        <div class="collapse navbar-collapse justify-content-end animation" id="navbarSupportedContent">
                            <div class="main-menu-darken"></div>
                            <div class="main-menu-content">
                                <div class="d-lg-none bg-primary p-2">
                                    <span class="text-white">{{ __('Menu') }}</span>
                                    <span class="float-right toggle-main-menu-offcanvas text-white">
                                        <i class="far fa-times-circle"></i>
                                    </span>
                                </div>
                                <div class="main-menu-nav d-lg-flex">
                                    {!!
                                        Menu::renderMenuLocation('main-menu', [
                                            'options' => ['class' => 'navbar-nav justify-content-end menu menu--mobile'],
                                            'view'    => 'main-menu',
                                        ])
                                    !!}
                                    @if (is_plugin_active('real-estate') && RealEstateHelper::isRegisterEnabled())
                                        <a class="btn btn-primary add-property" href="{{ route('public.account.properties.index') }}">
                                            <i class="fas fa-plus-circle"></i> {{ __('Adicionar Imóvel') }}
                                        </a>
                                    @endif

                                    <div class="d-sm-none">
                                        <div>
                                            @if (is_plugin_active('real-estate'))
                                                <ul class="topbar-items d-block">
                                                    <li class="login-item">
                                                        <a href="{{ route('public.wishlist') }}"><i class="fas fa-heart"></i> {{ __('Wishlist') }}(<span class="wishlist-count">0</span>)</a>
                                                    </li>
                                                </ul>
                                                @if (count($currencies) > 1)
                                                    <div class="language">
                                                        <div class="language-switcher-wrapper">
                                                            <div class="d-inline-block language-label">
                                                                {{ __('Currencies') }}:
                                                            </div>
                                                            <div class="dropdown d-inline-block">
                                                                <button class="btn btn-secondary dropdown-toggle btn-select-language" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                                    {{ get_application_currency()->title }}
                                                                </button>
                                                                <ul class="dropdown-menu language_bar_chooser">
                                                                    @foreach ($currencies as $currency)
                                                                        <li>
                                                                            <a href="{{ route('public.change-currency', $currency->title) }}" @if (get_application_currency_id() == $currency->id) class="active" @endif><span>{{ $currency->title }}</span></a>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                            {!! Theme::partial('language-switcher') !!}
                                            @if (is_plugin_active('real-estate'))
                                                <ul class="topbar-items d-block">
                                                    @if (auth('account')->check())
                                                        <li class="login-item"><a href="{{ route('public.account.dashboard') }}" rel="nofollow"><i class="fas fa-user"></i> <span>{{ auth('account')->user()->name }}</span></a></li>
                                                        <li class="login-item"><a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" rel="nofollow"><i class="fas fa-sign-out-alt"></i> {{ __('Logout') }}</a></li>
                                                    @else
                                                        <li class="login-item">
                                                            <a href="{{ route('public.account.login') }}"><i class="fas fa-sign-in-alt"></i>  {{ __('Login') }}</a>
                                                        </li>
                                                    @endif
                                                </ul>
                                                @if (is_plugin_active('real-estate') && auth('account')->check())
                                                    <form id="logout-form" action="{{ route('public.account.logout') }}" method="POST" style="display: none;">
                                                        @csrf
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    @php
        $page = Theme::get('page');
        
        // Definir as variáveis do banner independentemente da página
        $bannerType = theme_option('home_banner_type', 'image');
        $bannerImage = theme_option('home_banner') ? RvMedia::getImageUrl(theme_option('home_banner')) : Theme::asset()->url('images/banner.jpg');
        $bannerVideo = theme_option('home_banner_video') ? RvMedia::getImageUrl(theme_option('home_banner_video')) : '';
    @endphp
    @if (is_plugin_active('real-estate') && url()->current() == route('public.single') || ($page && $page->template === 'homepage'))
        @php
            $categories = get_property_categories(['indent' => '↳', 'conditions' => ['status' => \Srapid\Base\Enums\BaseStatusEnum::PUBLISHED]]);
        @endphp
        
        @if ($bannerType == 'video' && $bannerVideo)
            <div class="home_banner video-banner">
                <video autoplay muted loop playsinline id="home-background-video">
                    <source src="{{ $bannerVideo }}" type="video/mp4">
                </video>
                <div class="video-overlay"></div>
        @else
            <div class="home_banner" style="background-image: url({{ $bannerImage }})">
        @endif
                <div class="topsearch">
                    @if (theme_option('home_banner_description'))<h1 class="text-center text-white mb-4 banner-text-description">{{ theme_option('home_banner_description') }}</h1>@endif
                    <form @if (theme_option('enable_search_projects_on_homepage_search', 'yes') == 'yes') action="{{ route('public.projects') }}" @else action="{{ route('public.properties') }}" @endif method="GET" id="frmhomesearch">
                            <div class="typesearch" id="hometypesearch">
                                @if (theme_option('enable_search_projects_on_homepage_search', 'yes') == 'yes')
                                    <a href="javascript:void(0)" class="active" rel="project" data-url="{{ route('public.projects') }}">{{ __('Projects') }}</a>
                                @endif
                                <a href="javascript:void(0)" rel="sale" @if (theme_option('enable_search_projects_on_homepage_search', 'yes') != 'yes') class="active" @endif data-url="{{ route('public.properties') }}">{{ __('Sale') }}</a>
                                <a href="javascript:void(0)" rel="rent" data-url="{{ route('public.properties') }}">{{ __('Rent') }}</a>
                            </div>
                            <div class="input-group input-group-lg">

                                <input type="hidden" name="type" @if (theme_option('enable_search_projects_on_homepage_search', 'yes') == 'yes') value="project" @else value="sale" @endif id="txttypesearch">

                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-search"></i></span>
                                </div>
                                <div class="keyword-input">
                                    <input type="text" class="form-control" name="k" placeholder="{{ __('Enter keyword...') }}" id="txtkey" autocomplete="off">
                                    <div class="spinner-icon">
                                        <i class="fas fa-spin fa-spinner"></i>
                                    </div>
                                </div>
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-location"></i></span>
                                </div>
                                <div class="location-input" data-url="{{ route('public.ajax.cities') }}">
                                    <input type="hidden" name="city_id">
                                    <input class="select-city-state form-control" name="location" value="{{ request()->input('location') }}" placeholder="{{ __('City, State') }}" autocomplete="off">
                                    <div class="spinner-icon">
                                        <i class="fas fa-spin fa-spinner"></i>
                                    </div>
                                    <div class="suggestion">

                                    </div>
                                </div>
                                <div class="input-group-append search-button-wrapper">
                                    <button class="btn btn-orange" type="submit">{{ __('Search') }}</button>
                                </div>

                                <div class="advanced-search d-none d-sm-block">
                                    <a href="#" class="advanced-search-toggler">{{ __('Advanced') }} <i class="fas fa-caret-down"></i></a>
                                    <div class="advanced-search-content property-advanced-search">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-3 col-sm-6 pr-md-1">
                                                    {!! Theme::partial('real-estate.filters.categories', compact('categories')) !!}
                                                </div>
                                                <div class="col-md-3 col-sm-6 px-md-1">
                                                    {!! Theme::partial('real-estate.filters.bedroom') !!}
                                                </div>
                                                <div class="col-md-3 col-sm-6 px-md-1">
                                                    {!! Theme::partial('real-estate.filters.bathroom') !!}
                                                </div>
                                                <div class="col-md-3 col-sm-6 pl-md-1">
                                                    {!! Theme::partial('real-estate.filters.floor') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="advanced-search-content project-advanced-search">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    {!! Theme::partial('real-estate.filters.categories', compact('categories')) !!}
                                                </div>
                                                <div class="col-md-8">
                                                    {!! Theme::partial('real-estate.filters.price') !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="listsuggest">

                            </div>
                        </form>
                </div>
            </div>
        </div>
    @endif
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var video = document.getElementById('home-background-video');
    if (video) {
        video.addEventListener('error', function(e) {
            console.error('Erro ao carregar o vídeo:', e);
            // Fallback para imagem se o vídeo falhar
            var banner = document.querySelector('.home_banner');
            if (banner) {
                banner.classList.remove('video-banner');
                banner.style.backgroundImage = 'url("{{ theme_option("home_banner") ? RvMedia::getImageUrl(theme_option("home_banner")) : Theme::asset()->url("images/banner.jpg") }}")';
                
                // Remover elementos de vídeo
                var videoElement = document.getElementById('home-background-video');
                var overlayElement = document.querySelector('.video-overlay');
                
                if (videoElement) videoElement.remove();
                if (overlayElement) overlayElement.remove();
            }
        });
        
        // Força o carregamento do vídeo
        video.load();
    }
});
</script>
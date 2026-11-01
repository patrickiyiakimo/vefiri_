@extends('layouts.storefront')

@section('title', 'AliExpress - Affordable Chinese Stores & Free Shipping')

@section('content')
    <x-sections.hero
        :slides="$heroSlides"
        :menu-categories="$menuCategories"
    />

    <x-sections.quick-categories :categories="$quickCategories" />

    <x-sections.todays-deals :tabs="$dealTabs" />

    <x-sections.business :stats="$businessStats" />

    <x-sections.promo-banners :banners="$promoBanners" />

    <x-sections.super-deals :products="$superDeals" />

    <x-sections.more-to-love :tabs="$recommendationTabs" />

    <x-sections.newsletter />
@endsection

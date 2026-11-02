@extends('layouts.storefront')

@section('title', 'Vefiri | Shop quality products with nationwide delivery')
@section('description', 'Discover trending products, everyday essentials and exclusive deals on Vefiri. Enjoy secure payments, buyer protection and delivery nationwide across Nigeria.')

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

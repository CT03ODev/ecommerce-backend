<?php

namespace App\Providers\Filament;

use App\Filament\Resources\BlogCategoryResource;
use App\Filament\Resources\BlogPostResource;
use App\Filament\Resources\UserResource;
use App\Filament\Widgets\AdvancedRevenueChart;
use App\Http\Middleware\Authenticate;
use App\Providers\Filament\CustomAvatarsProvider;
// use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\ProductBrandChart;
use App\Filament\Widgets\ProductStatsOverview;
use App\Filament\Widgets\UserStatsOverview;
use App\Filament\Widgets\ProductCategoryChart;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->login(null)
            ->path('admin/dashboard')
            ->favicon(asset('favicon.ico'))
            ->brandName(config('app.name'))
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->defaultAvatarProvider(CustomAvatarsProvider::class)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                StatsOverview::class,
                AdvancedRevenueChart::class,
                ProductStatsOverview::class,
                UserStatsOverview::class,
                ProductCategoryChart::class,
                ProductBrandChart::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
            // ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
            //     return $builder
            //         ->items([
            //             NavigationItem::make('Dashboard')
            //                 ->icon('heroicon-o-home')
            //                 ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.dashboard'))
            //                 ->url(fn (): string => Dashboard::getUrl()),
            //         ])
            //         ->groups([
            //             NavigationGroup::make('Blog')
            //                 ->items([
            //                     ...BlogCategoryResource::getNavigationItems(),
            //                     ...BlogPostResource::getNavigationItems(),
            //                 ]),
            //             NavigationGroup::make('Settings')
            //                 ->items([
            //                     ...UserResource::getNavigationItems(),
            //                     ...PermissionResource::getNavigationItems(),
            //                     ...RoleResource::getNavigationItems(),
            //                 ])
            //         ]);
            // });
    }
}

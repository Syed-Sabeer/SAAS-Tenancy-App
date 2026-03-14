# SaaS Multi-Tenancy Structure Plan

## Core Layout

app/
  Http/
    Controllers/
      Central/
        Auth/
        DashboardController.php
        CompanyController.php
        TenantProvisionController.php
      Tenant/
        Auth/
        DashboardController.php
        UserController.php
        ProfileController.php
    Middleware/
      EnsureEnterpriseAdmin.php
      InitializeTenantByDomain.php
      CheckTenantRole.php
  Jobs/
    CreateTenantDatabaseJob.php
    RegisterTenantJob.php
    AttachTenantDomainJob.php
    RunTenantMigrationsJob.php
    SeedTenantDatabaseJob.php
    CreateTenantAdminJob.php
    FinalizeTenantOnboardingJob.php
  Models/
    Central/
      EnterpriseAdmin.php
      Company.php
      CompanyOnboardingRequest.php
      Plan.php
      CompanySubscription.php
      ProvisioningLog.php
    Tenant/
      User.php
      TenantCompanyProfile.php
      Role.php
      Permission.php
  Services/
    CompanyOnboardingService.php
    DomainGeneratorService.php
    TenantProvisioningService.php
    TenantUserService.php

config/
  auth.php
  tenancy.php

database/
  migrations/
    central/
    tenant/
  seeders/
    CentralDatabaseSeeder.php
    TenantDatabaseSeeder.php

resources/
  js/
    central/
      components/
      pages/
    tenant/
      components/
      pages/

routes/
  central.php
  tenant.php
  web.php
  api.php
<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use Config\App;
use Config\Format;

final class ProductionReadinessTest extends CIUnitTestCase
{
    public function testUpdatedFrameworkConfigIsPresent(): void
    {
        $app = new App();
        $format = new Format();

        $this->assertSame('a-z 0-9~%.:_\-', $app->permittedURIChars);
        $this->assertSame(512, $format->jsonEncodeDepth);
    }

    public function testFrontControllersUseSupportedBootClass(): void
    {
        $publicIndex = file_get_contents(ROOTPATH . 'public/index.php');
        $spark = file_get_contents(ROOTPATH . 'spark');

        $this->assertStringContainsString('Boot::bootWeb($paths)', $publicIndex);
        $this->assertStringContainsString('Boot::bootSpark($paths)', $spark);
        $this->assertStringNotContainsString('systemDirectory,', $publicIndex);
        $this->assertStringNotContainsString('bootstrap.php', $spark);
    }

    public function testLoginCredentialShortcutsMatchDocumentedRoles(): void
    {
        $loginView = file_get_contents(APPPATH . 'Views/auth/login.php');

        foreach ([
            'admin' => 'Admin@2026',
            'purchase' => 'Purchase@2026',
            'sales' => 'Sales@2026',
            'production' => 'Production@2026',
            'finance' => 'Finance@2026',
            'gate_entry' => 'Gate@2026',
            'hrm' => 'Hrm@2026',
            'reception' => 'Reception@2026',
        ] as $username => $password) {
            $this->assertStringContainsString("'username' => '{$username}'", $loginView);
            $this->assertStringContainsString("'password' => '{$password}'", $loginView);
        }
    }
}

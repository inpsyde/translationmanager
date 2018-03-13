<?php
/**
 * ProjectTest
 *
 * @author    Guido Scialfa <dev@guidoscialfa.com>
 * @package   Translation Manager
 * @copyright Copyright (c) 2018, Guido Scialfa
 * @license   GNU General Public License, version 2
 *
 * Copyright (C) 2018 Guido Scialfa <dev@guidoscialfa.com>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace Translationmanager\Tests\Domain;

use Translationmanager\Domain\Project;
use Translationmanager\Tests\TestCase;

class ProjectTest extends TestCase {

	public function testInstance() {

		$testee = new Project(
			'WordPress',
			'1.0.0',
			'translationmanager',
			'1.0.0',
			'Project Term Name'
		);

		$this->assertInstanceOf( 'TranslationManager\\Domain\\Project', $testee );
	}

	public function testTo_header_array() {

		$testee = new Project(
			'WordPress',
			'1.0.0',
			'translationmanager',
			'1.0.0',
			'Project Term Name'
		);

		$expected = [
			'X-System'         => 'WordPress',
			'X-System-Version' => '1.0.0',
			'X-Plugin'         => 'translationmanager',
			'X-Plugin-Version' => '1.0.0',
			'X-Name'           => 'Project Term Name',
			'X-Type'           => 'quote',
			'X-Callback'       => null,
		];

		$this->assertSame( $expected, $testee->to_header_array() );
	}
}

<?php
namespace Webfox\T3events\Tests\Unit\Domain\Repository;

use CPSIT\ZewEvents\Domain\Model\Dto\PerformanceDemand;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use Webfox\T3events\Domain\Model\Dto\DemandInterface;
use Webfox\T3events\Domain\Repository\PerformanceRepository;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 Dirk Wenzel <dirk.wenzel@cps-it.de>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Class PerformanceRepositoryTest
 *
 * @package Webfox\T3events\Tests\Unit\Domain\Repository
 * @coversDefaultClass \Webfox\T3events\Domain\Repository\PerformanceRepository
 */
class PerformanceRepositoryTest extends UnitTestCase {

	/**
	 * @var PerformanceRepository
	 */
	protected $subject;

	/**
	 *
	 */
	public function setUp() {
		$this->subject = $this->getAccessibleMock(
			PerformanceRepository::class,
			['dummy'], [], '', false
		);
	}

	/**
	 * @test
	 * @covers ::createConstraintsFromDemand
	 */

	/**
	 * @test
	 * @covers ::createConstraintsFromDemand
	 */
	public function createConstraintsFromDemandInitiallyReturnsEmptyArray() {
		$demand = $this->getMock(
			DemandInterface::class
		);
		$query = $this->getMock(
			QueryInterface::class,
			[], [], '', FALSE
		);

		$this->assertEquals(
			[],
			$this->subject->_call('createConstraintsFromDemand', $query, $demand)
		);
	}

	/**
	 * @test
	 * @covers ::createConstraintsFromDemand
	 */
	public function createConstraintsFromDemandCallsCreateStatusConstraints() {
		$this->subject = $this->getAccessibleMock(
			PerformanceRepository::class,
			['createStatusConstraints'], [], '', false);
		$demand = $this->getMock(
			DemandInterface::class
		);
		$query = $this->getMock(
			QueryInterface::class,
			[], [], '', false
		);

		$this->subject->expects($this->once())
			->method('createStatusConstraints')
			->with($query, $demand);
		$this->subject->_call('createConstraintsFromDemand', $query, $demand);
	}

	/**
	 * @test
	 */
	public function createConstraintsFromDemandCombinesStatusConstraintsLogicalOr() {
		$this->subject = $this->getAccessibleMock(
			PerformanceRepository::class,
			['createStatusConstraints', 'combineConstraints'], [], '', false);
		$demand = $this->getMock(
			DemandInterface::class
		);
		$query = $this->getMock(
			QueryInterface::class,
			[], [], '', false
		);

		$constraints = [];
		$mockStatusConstraints = ['foo'];

		$this->subject->expects($this->once())
			->method('createStatusConstraints')
			->with($query, $demand)
			->will($this->returnValue($mockStatusConstraints)
			);
		$this->subject->expects($this->once())
			->method('combineConstraints')
			->with($query, $constraints, $mockStatusConstraints, 'OR');

		$this->subject->_call('createConstraintsFromDemand', $query, $demand);
	}


	/**
	 * @test
	 */
	public function createConstraintsFromDemandCombinesStatusConstraintsLogicalNotOr() {
		$this->subject = $this->getAccessibleMock(
			PerformanceRepository::class,
			['createStatusConstraints', 'combineConstraints'], [], '', false);
		$demand = $this->getMock(
			DemandInterface::class, ['isExcludeSelectedStatuses']
		);
		$query = $this->getMock(
			QueryInterface::class,
			[], [], '', false
		);

		$constraints = [];
		$mockStatusConstraints = ['foo'];

		$demand->expects($this->once())
			->method('isExcludeSelectedStatuses')
			->will($this->returnValue(true));

		$this->subject->expects($this->once())
			->method('createStatusConstraints')
			->with($query, $demand)
			->will($this->returnValue($mockStatusConstraints)
			);
		$this->subject->expects($this->once())
			->method('combineConstraints')
			->with($query, $constraints, $mockStatusConstraints, 'NOTOR');

		$this->subject->_call('createConstraintsFromDemand', $query, $demand);
	}

}
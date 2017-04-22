<?php
namespace TYPO3\CMS\Typo3DbLegacy\Tests\Unit\Database;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Typo3DbLegacy\Database\DatabaseConnection;
use TYPO3\CMS\Typo3DbLegacy\Database\PreparedStatement;

/**
 * Test case
 */
class PreparedStatementTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|DatabaseConnection
     */
    protected $databaseStub;

    /**
     * Create a new database mock object for every test
     * and backup the original global database object.
     */
    protected function setUp()
    {
        $this->databaseStub = $this->setUpAndReturnDatabaseStub();
    }

    //////////////////////
    // Utility functions
    //////////////////////
    /**
     * Set up the stub to be able to get the result of the prepared statement.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function setUpAndReturnDatabaseStub()
    {
        $GLOBALS['TYPO3_DB'] = $this->getAccessibleMock(
            DatabaseConnection::class,
            ['prepare_PREPAREDquery'],
            [],
            '',
            false,
            false
        );

        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * Create an object fo the subject to be tested.
     *
     * @param string $query
     * @return PreparedStatement
     */
    private function createPreparedStatement($query)
    {
        return new PreparedStatement($query, 'pages');
    }

    ////////////////////////////////////
    // Tests for the utility functions
    ////////////////////////////////////

    /**
     * @test
     */
    public function setUpAndReturnDatabaseStubReturnsMockObjectOfDatabaseConnection()
    {
        $this->assertTrue($this->setUpAndReturnDatabaseStub() instanceof DatabaseConnection);
    }

    /**
     * @test
     */
    public function createPreparedStatementReturnsInstanceOfPreparedStatementClass()
    {
        $this->assertTrue($this->createPreparedStatement('dummy') instanceof PreparedStatement);
    }

    ///////////////////////////////////////
    // Tests for \TYPO3\CMS\Core\Database\PreparedStatement
    ///////////////////////////////////////
    /**
     * Data Provider for two tests, providing sample queries, parameters and expected result queries.
     *
     * @see parametersAreReplacedInQueryByCallingExecute
     * @see parametersAreReplacedInQueryWhenBoundWithBindValues
     * @return array
     */
    public function parametersAndQueriesDataProvider()
    {
        return [
            'one named integer parameter' => [
                'SELECT * FROM pages WHERE pid=:pid',
                [':pid' => 1],
                'SELECT * FROM pages WHERE pid=?'
            ],
            'one unnamed integer parameter' => [
                'SELECT * FROM pages WHERE pid=?',
                [1],
                'SELECT * FROM pages WHERE pid=?'
            ],
            'one named integer parameter is replaced multiple times' => [
                'SELECT * FROM pages WHERE pid=:pid OR uid=:pid',
                [':pid' => 1],
                'SELECT * FROM pages WHERE pid=? OR uid=?'
            ],
            'two named integer parameters are replaced' => [
                'SELECT * FROM pages WHERE pid=:pid OR uid=:uid',
                [':pid' => 1, ':uid' => 10],
                'SELECT * FROM pages WHERE pid=? OR uid=?'
            ],
            'two unnamed integer parameters are replaced' => [
                'SELECT * FROM pages WHERE pid=? OR uid=?',
                [1, 1],
                'SELECT * FROM pages WHERE pid=? OR uid=?'
            ],
        ];
    }

    /**
     * Checking if calling execute() with parameters, they are
     * properly replaced in the query.
     *
     * @test
     * @dataProvider parametersAndQueriesDataProvider
     * @param string $query Query with unreplaced markers
     * @param array  $parameters Array of parameters to be replaced in the query
     * @param string $expectedResult Query with all markers replaced
     */
    public function parametersAreReplacedByQuestionMarkInQueryByCallingExecute($query, $parameters, $expectedResult)
    {
        $statement = $this->createPreparedStatement($query);
        $this->databaseStub->expects($this->any())
            ->method('prepare_PREPAREDquery')
            ->with($this->equalTo($expectedResult));
        $statement->execute($parameters);
    }

    /**
     * Checking if parameters bound to the statement by bindValues()
     * are properly replaced in the query.
     *
     * @test
     * @dataProvider parametersAndQueriesDataProvider
     * @param string $query Query with unreplaced markers
     * @param array  $parameters Array of parameters to be replaced in the query
     * @param string $expectedResult Query with all markers replaced
     */
    public function parametersAreReplacedInQueryWhenBoundWithBindValues($query, $parameters, $expectedResult)
    {
        $statement = $this->createPreparedStatement($query);
        $this->databaseStub->expects($this->any())
            ->method('prepare_PREPAREDquery')
            ->with($this->equalTo($expectedResult));
        $statement->bindValues($parameters);
        $statement->execute();
    }

    /**
     * Data Provider with invalid parameters.
     *
     * @see invalidParameterTypesPassedToBindValueThrowsException
     * @return array
     */
    public function invalidParameterTypesPassedToBindValueThrowsExceptionDataProvider()
    {
        return [
            'integer passed with param type NULL' => [
                1,
                PreparedStatement::PARAM_NULL,
                1282489834
            ],
            'string passed with param type NULL' => [
                '1',
                PreparedStatement::PARAM_NULL,
                1282489834
            ],
            'bool passed with param type NULL' => [
                true,
                PreparedStatement::PARAM_NULL,
                1282489834
            ],
            'NULL passed with param type INT' => [
                null,
                PreparedStatement::PARAM_INT,
                1281868686
            ],
            'string passed with param type INT' => [
                '1',
                PreparedStatement::PARAM_INT,
                1281868686
            ],
            'bool passed with param type INT' => [
                true,
                PreparedStatement::PARAM_INT,
                1281868686
            ],
            'NULL passed with param type BOOL' => [
                null,
                PreparedStatement::PARAM_BOOL,
                1281868687
            ],
            'string passed with param type BOOL' => [
                '1',
                PreparedStatement::PARAM_BOOL,
                1281868687
            ],
            'integer passed with param type BOOL' => [
                1,
                PreparedStatement::PARAM_BOOL,
                1281868687
            ]
        ];
    }

    /**
     * Checking if an exception is thrown if invalid parameters are
     * provided vor bindValue().
     *
     * @test
     * @dataProvider invalidParameterTypesPassedToBindValueThrowsExceptionDataProvider
     * @param mixed $parameter Parameter to be replaced in the query
     * @param int $type Type of the parameter value
     * @param int $exceptionCode Expected exception code
     */
    public function invalidParameterTypesPassedToBindValueThrowsException($parameter, $type, $exceptionCode)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode($exceptionCode);

        $statement = $this->createPreparedStatement('');
        $statement->bindValue(1, $parameter, $type);
    }

    /**
     * Data Provider for invalid marker names.
     *
     * @see passingInvalidMarkersThrowsExeption
     * @return array
     */
    public function passingInvalidMarkersThrowsExceptionDataProvider()
    {
        return [
            'using other prefix than colon' => [
                /** @lang text */
                'SELECT * FROM pages WHERE pid=#pid',
                ['#pid' => 1]
            ],
            'using non alphanumerical character' => [
                /** @lang text */
                'SELECT * FROM pages WHERE title=:stra≠e',
                [':stra≠e' => 1]
            ],
            'no colon used' => [
                /** @lang text */
                'SELECT * FROM pages WHERE pid=pid',
                ['pid' => 1]
            ],
            'colon at the end' => [
                /** @lang text */
                'SELECT * FROM pages WHERE pid=pid:',
                ['pid:' => 1]
            ],
            'colon without alphanumerical character' => [
                /** @lang text */
                'SELECT * FROM pages WHERE pid=:',
                [':' => 1]
            ]
        ];
    }

    /**
     * Checks if an exception is thrown, if parameter have invalid marker named.
     *
     * @test
     * @dataProvider passingInvalidMarkersThrowsExceptionDataProvider
     * @param string $query Query with unreplaced markers
     * @param array  $parameters Array of parameters to be replaced in the query
     */
    public function passingInvalidMarkersThrowsException($query, $parameters)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionCode(1395055513);

        $statement = $this->createPreparedStatement($query);
        $statement->bindValues($parameters);
    }
}

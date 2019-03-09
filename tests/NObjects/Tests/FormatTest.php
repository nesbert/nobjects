<?php
namespace NObjects\Tests;

use NObjects\Format;

class FormatTest extends \PHPUnit\Framework\TestCase
{
    public function testDefaults()
    {
        $this->assertEquals('50%', Format::toPercent(50, 100));
        $this->assertEquals('101', Format::toNumber(100.55));
    }

    public function testNumberOfDecimals()
    {
        $this->assertNull(Format::getNumberOfDecimals());
        Format::setNumberOfDecimals(3);
        $this->assertEquals(3, Format::getNumberOfDecimals());
    }

    public function testNumberOfDecimalsPercent()
    {
        $this->assertNull(Format::getNumberOfDecimalsPercent());
        Format::setNumberOfDecimalsPercent(2);
        $this->assertEquals(2, Format::getNumberOfDecimalsPercent());
    }

    public function testToPercent()
    {
        $this->assertEquals('100.00%', Format::toPercent(100, 100, 2));
        $this->assertEquals('100.0%', Format::toPercent(100, 100, 1));
        $this->assertEquals('100%', Format::toPercent(100, 100, 0));
        $this->assertEquals('50.00%', Format::toPercent(50, 100, 2));
        $this->assertEquals('50.0%', Format::toPercent(50, 100, 1));
        $this->assertEquals('50%', Format::toPercent(50, 100, 0));
        $this->assertEquals('10.25%', Format::toPercent(10.25, 100, 2));
        $this->assertEquals('10.3%', Format::toPercent(10.25, 100, 1));
        $this->assertEquals('10%', Format::toPercent(10.25, 100, 0));
        $this->assertEquals('5.55%', Format::toPercent(5.55, 100, 2));
        $this->assertEquals('5.6%', Format::toPercent(5.55, 100, 1));
        $this->assertEquals('6%', Format::toPercent(5.55, 100, 0));

        $this->assertEquals('0.00%', Format::toPercent(0, 100, 2));
        $this->assertEquals('0.0%', Format::toPercent(0, 100, 1));
        $this->assertEquals('0%', Format::toPercent(0, 100, 0));

        $this->assertEquals('0.00%', Format::toPercent(0, 0, 2));
        $this->assertEquals('0.0%', Format::toPercent(0, 0, 1));
        $this->assertEquals('0%', Format::toPercent(0, 0, 0));

        $this->assertEquals('0.00%', Format::toPercent(50, 0, 2));
        $this->assertEquals('0.0%', Format::toPercent(40, 0, 1));
        $this->assertEquals('0%', Format::toPercent(20, 0, 0));

        Format::setNumberOfDecimalsPercent(3);
        $this->assertEquals('10.212%', Format::toPercent(25.53, 250));
    }

    public function testToNumber()
    {
        $this->assertEquals('100.00', Format::toNumber(100, 2));
        $this->assertEquals('100.0', Format::toNumber(100, 1));
        $this->assertEquals('100', Format::toNumber(100, 0));
        $this->assertEquals('50.00', Format::toNumber(50, 2));
        $this->assertEquals('50.0', Format::toNumber(50, 1));
        $this->assertEquals('50', Format::toNumber(50, 0));
        $this->assertEquals('10.25', Format::toNumber(10.25, 2));
        $this->assertEquals('10.3', Format::toNumber(10.25, 1));
        $this->assertEquals('10', Format::toNumber(10.25, 0));
        $this->assertEquals('5.55', Format::toNumber(5.55, 2));
        $this->assertEquals('5.6', Format::toNumber(5.55, 1));
        $this->assertEquals('6', Format::toNumber(5.55, 0));

        Format::setNumberOfDecimalsPercent(3);
        $this->assertEquals('10.216', Format::toNumber(10.2156));
    }
}

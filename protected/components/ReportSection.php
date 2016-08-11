<?php

/**
 * Report section class
 */
class ReportSection {
    /**
     * Built-in section types
     */
    const TYPE_INTRO = 10;
    const TYPE_CHART_SECURITY_LEVEL = 20;
    const TYPE_CHART_VULN_DISTR = 30;
    const TYPE_CHART_VULN_DEGREE = 40;
    const TYPE_RISK_MATRIX = 50;
    const TYPE_REDUCED_VULN_LIST = 60;
    const TYPE_VULNS = 70;
    const TYPE_INFO_CHECKS = 80;
    const TYPE_APPENDIX = 90;
    const TYPE_FOOTER = 100;
    const TYPE_CUSTOM = 200;

    /**
     * Get type titles
     * @return array
     */
    public static function getTypeTitles() {
        return [
            self::TYPE_INTRO => Yii::t("app", "Intro"),
            self::TYPE_RISK_MATRIX => Yii::t("app", "Risk Matrix"),
            self::TYPE_REDUCED_VULN_LIST => Yii::t("app", "Reduced Vulnerability List"),
            self::TYPE_VULNS => Yii::t("app", "Vulnerability List"),
            self::TYPE_INFO_CHECKS => Yii::t("app", "Info Checks"),
            self::TYPE_APPENDIX => Yii::t("app", "Appendix"),
            self::TYPE_FOOTER => Yii::t("app", "Footer"),
            self::TYPE_CHART_SECURITY_LEVEL => Yii::t("app", "Security Level"),
            self::TYPE_CHART_VULN_DISTR => Yii::t("app", "Vulnerability Distribution"),
            self::TYPE_CHART_VULN_DEGREE => Yii::t("app", "Degree of Fulfillment"),
            self::TYPE_CUSTOM => Yii::t("app", "Custom"),
        ];
    }

    /**
     * Get chart types
     * @return array
     */
    public static function getChartTypes() {
        return [
            self::TYPE_CHART_SECURITY_LEVEL,
            self::TYPE_CHART_VULN_DISTR,
            self::TYPE_CHART_VULN_DEGREE,
        ];
    }

    /**
     * Get valid types
     * @return array
     */
    public static function getValidTypes() {
        return [
            self::TYPE_CUSTOM,
            self::TYPE_INTRO,
            self::TYPE_RISK_MATRIX,
            self::TYPE_REDUCED_VULN_LIST,
            self::TYPE_VULNS,
            self::TYPE_INFO_CHECKS,
            self::TYPE_APPENDIX,
            self::TYPE_FOOTER,
            self::TYPE_CHART_SECURITY_LEVEL,
            self::TYPE_CHART_VULN_DISTR,
            self::TYPE_CHART_VULN_DEGREE,
        ];
    }

    /**
     * Check if section type is chart
     * @param $section
     * @return bool
     */
    public static function isChart($section) {
        return in_array($section, self::getChartTypes());
    }
}

{{-- Global Investment Calculator Script --}}
@once
@push('script')
<script>
/**
 * Global Investment Calculator
 * Handles investment return calculations with support for fractional capital return
 *
 * Usage:
 * const result = InvestmentCalculator.calculate({
 *     investmentAmount: 1000,
 *     interestType: 1, // 1 = percentage, 0 = fixed
 *     interest: 10, // percentage or fixed amount
 *     repeatTime: 12, // months
 *     lifetime: 0, // 0 = repeat, 1 = lifetime
 *     capitalBack: 1, // 1 = yes, 0 = no
 *     capitalMonthsReturn: 3, // month to start capital return
 *     fractionalCapital: true, // calculate with remaining capital
 *     interestDistribution: null // or array of segments
 * });
 */
window.InvestmentCalculator = (function() {
    'use strict';

    /**
     * Calculate investment returns
     * @param {Object} options - Calculation parameters
     * @returns {Object} - Calculation results with monthly breakdown
     */
    function calculate(options) {
        const {
            investmentAmount,
            interestType = 1, // 1 = percentage, 0 = fixed
            interest,
            repeatTime = 12,
            lifetime = 0,
            capitalBack = 0,
            capitalMonthsReturn = 0,
            fractionalCapital = false,
            compoundInterest = 0,
            interestDistribution = null
        } = options;

        if (investmentAmount <= 0) {
            return getEmptyResult();
        }

        const totalMonths = lifetime === 1 ? 12 : repeatTime;

        // Choose calculation method based on interest distribution
        if (interestDistribution && interestDistribution.length > 0) {
            return calculateWithInterestDistribution({
                investmentAmount,
                interestType,
                interest,
                totalMonths: repeatTime,
                capitalBack,
                capitalMonthsReturn,
                fractionalCapital,
                interestDistribution
            });
        } else {
            return calculateStandardPlan({
                investmentAmount,
                interestType,
                interest,
                totalMonths,
                capitalBack,
                capitalMonthsReturn,
                fractionalCapital,
                compoundInterest
            });
        }
    }

    /**
     * Calculate standard plan returns (without interest distribution)
     */
    function calculateStandardPlan(options) {
        const {
            investmentAmount,
            interestType,
            interest,
            totalMonths,
            capitalBack,
            capitalMonthsReturn,
            fractionalCapital,
            compoundInterest
        } = options;

        const monthlyBreakdown = [];
        let totalInterest = 0;
        let totalCapitalReturn = 0;
        let cumulativeBalance = 0;
        let remainingCapital = investmentAmount;
        let capitalPerMonth = 0;

        // Calculate capital return per month if fractional
        if (capitalBack === 1 && capitalMonthsReturn > 0) {
            const remainingMonths = totalMonths - capitalMonthsReturn + 1;
            capitalPerMonth = investmentAmount / remainingMonths;
        }

        // Calculate base monthly interest rate
        let baseMonthlyInterestRate = 0;
        if (interestType === 1) {
            baseMonthlyInterestRate = interest / totalMonths;
        }

        for (let month = 1; month <= totalMonths; month++) {
            // Calculate interest for this month
            let monthlyInterest = 0;

            if (interestType === 1) {
                // Percentage-based interest
                if (fractionalCapital && capitalBack === 1 && capitalMonthsReturn > 0) {
                    // Calculate interest on remaining capital
                    monthlyInterest = remainingCapital * (baseMonthlyInterestRate / 100);
                } else {
                    // Calculate interest on original amount
                    monthlyInterest = investmentAmount * (baseMonthlyInterestRate / 100);
                }
            } else {
                // Fixed interest (divided among months)
                monthlyInterest = interest / totalMonths;
            }

            // Calculate capital return for this month
            let monthlyCapitalReturn = 0;
            if (capitalBack === 1 && capitalMonthsReturn > 0 && month >= capitalMonthsReturn) {
                monthlyCapitalReturn = capitalPerMonth;
                totalCapitalReturn += monthlyCapitalReturn;

                // Reduce remaining capital for next month's calculation
                remainingCapital -= monthlyCapitalReturn;
                if (remainingCapital < 0) remainingCapital = 0;
            }

            totalInterest += monthlyInterest;
            const monthlyTotal = monthlyInterest + monthlyCapitalReturn;
            cumulativeBalance += monthlyInterest;

            monthlyBreakdown.push({
                month: month,
                interest: monthlyInterest,
                capitalReturn: monthlyCapitalReturn,
                total: monthlyTotal,
                balance: cumulativeBalance,
                remainingCapital: remainingCapital
            });
        }

        // If capital back but NOT fractional, add final capital return row
        if (capitalBack === 1 && capitalMonthsReturn === 0) {
            monthlyBreakdown.push({
                month: 'Final',
                interest: 0,
                capitalReturn: investmentAmount,
                total: investmentAmount,
                balance: cumulativeBalance + investmentAmount,
                remainingCapital: 0,
                isFinalCapitalReturn: true
            });
            totalCapitalReturn = investmentAmount;
        }

        return {
            totalInterest,
            totalCapitalReturn,
            totalReturn: totalInterest + totalCapitalReturn,
            roi: (totalInterest / investmentAmount) * 100,
            monthlyBreakdown,
            investmentAmount
        };
    }

    /**
     * Calculate with interest distribution segments
     */
    function calculateWithInterestDistribution(options) {
        const {
            investmentAmount,
            interestType,
            interest,
            totalMonths,
            capitalBack,
            capitalMonthsReturn,
            fractionalCapital,
            interestDistribution
        } = options;

        const monthlyBreakdown = [];
        let totalInterest = 0;
        let totalCapitalReturn = 0;
        let cumulativeBalance = 0;
        let remainingCapital = investmentAmount;
        let currentMonth = 1;

        // Calculate capital return per month if fractional
        let capitalPerMonth = 0;
        if (capitalBack === 1 && capitalMonthsReturn > 0) {
            const remainingMonths = totalMonths - capitalMonthsReturn + 1;
            capitalPerMonth = investmentAmount / remainingMonths;
        }

        // Process each segment
        interestDistribution.forEach(segment => {
            const segmentMonths = segment.months;
            const segmentPercentage = segment.percentage;
            const monthlyRate = segmentPercentage / segmentMonths;

            for (let i = 0; i < segmentMonths; i++) {
                // Calculate interest for this month
                let monthlyInterest = 0;

                if (fractionalCapital && capitalBack === 1 && capitalMonthsReturn > 0) {
                    // Calculate interest on remaining capital
                    monthlyInterest = remainingCapital * (monthlyRate / 100);
                } else {
                    // Calculate interest on original amount
                    monthlyInterest = investmentAmount * (monthlyRate / 100);
                }

                // Calculate capital return for this month
                let monthlyCapitalReturn = 0;
                if (capitalBack === 1 && capitalMonthsReturn > 0 && currentMonth >= capitalMonthsReturn) {
                    monthlyCapitalReturn = capitalPerMonth;
                    totalCapitalReturn += monthlyCapitalReturn;

                    // Reduce remaining capital for next month's calculation
                    remainingCapital -= monthlyCapitalReturn;
                    if (remainingCapital < 0) remainingCapital = 0;
                }

                totalInterest += monthlyInterest;
                const monthlyTotal = monthlyInterest + monthlyCapitalReturn;
                cumulativeBalance += monthlyInterest;

                monthlyBreakdown.push({
                    month: currentMonth,
                    interest: monthlyInterest,
                    capitalReturn: monthlyCapitalReturn,
                    total: monthlyTotal,
                    balance: cumulativeBalance,
                    remainingCapital: remainingCapital,
                    segment: segment.description
                });

                currentMonth++;
            }
        });

        // If capital back but NOT fractional, add final capital return row
        if (capitalBack === 1 && capitalMonthsReturn === 0) {
            monthlyBreakdown.push({
                month: 'Final',
                interest: 0,
                capitalReturn: investmentAmount,
                total: investmentAmount,
                balance: cumulativeBalance + investmentAmount,
                remainingCapital: 0,
                isFinalCapitalReturn: true
            });
            totalCapitalReturn = investmentAmount;
        }

        return {
            totalInterest,
            totalCapitalReturn,
            totalReturn: totalInterest + totalCapitalReturn,
            roi: (totalInterest / investmentAmount) * 100,
            monthlyBreakdown,
            investmentAmount
        };
    }

    /**
     * Get empty result object
     */
    function getEmptyResult() {
        return {
            totalInterest: 0,
            totalCapitalReturn: 0,
            totalReturn: 0,
            roi: 0,
            monthlyBreakdown: [],
            investmentAmount: 0
        };
    }

    /**
     * Format amount with currency
     */
    function formatAmount(amount, currencySymbol) {
        currencySymbol = currencySymbol || '{{ gs("cur_text") }}';
        return currencySymbol + ' ' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    /**
     * Calculate summary for modal display (simplified version)
     */
    function calculateSummary(options) {
        const result = calculate(options);
        return {
            totalInterest: result.totalInterest,
            totalReturn: result.totalReturn,
            roi: result.roi,
            formattedTotalInterest: formatAmount(result.totalInterest, options.currencySymbol),
            formattedTotalReturn: formatAmount(result.totalReturn, options.currencySymbol)
        };
    }

    // Public API
    return {
        calculate: calculate,
        calculateSummary: calculateSummary,
        formatAmount: formatAmount
    };
})();
</script>
@endpush
@endonce

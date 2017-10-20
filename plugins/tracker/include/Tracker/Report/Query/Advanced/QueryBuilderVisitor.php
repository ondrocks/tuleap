<?php
/**
 * Copyright (c) Enalean, 2016-2017. All Rights Reserved.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

namespace Tuleap\Tracker\Report\Query\Advanced;

use Tracker;
use Tracker_FormElementFactory;
use Tuleap\Tracker\Report\Query\Advanced\Grammar\AndExpression;
use Tuleap\Tracker\Report\Query\Advanced\Grammar\AndOperand;
use Tuleap\Tracker\Report\Query\Advanced\Grammar\BetweenComparison;
use Tuleap\Tracker\Report\Query\Advanced\Grammar\EqualComparison;
use Tuleap\Tracker\Report\Query\Advanced\Grammar\GreaterThanComparison;
use Tuleap\Tracker\Report\Query\Advanced\Grammar\GreaterThanOrEqualComparison;
use Tuleap\Tracker\Report\Query\Advanced\Grammar\InComparison;
use Tuleap\Tracker\Report\Query\Advanced\Grammar\LesserThanComparison;
use Tuleap\Tracker\Report\Query\Advanced\Grammar\LesserThanOrEqualComparison;
use Tuleap\Tracker\Report\Query\Advanced\Grammar\NotEqualComparison;
use Tuleap\Tracker\Report\Query\Advanced\Grammar\NotInComparison;
use Tuleap\Tracker\Report\Query\Advanced\Grammar\OrExpression;
use Tuleap\Tracker\Report\Query\Advanced\Grammar\OrOperand;
use Tuleap\Tracker\Report\Query\Advanced\Grammar\Visitable;
use Tuleap\Tracker\Report\Query\Advanced\Grammar\Visitor;
use Tuleap\Tracker\Report\Query\Advanced\QueryBuilder\BetweenFieldComparisonVisitor;
use Tuleap\Tracker\Report\Query\Advanced\QueryBuilder\EqualFieldComparisonVisitor;
use Tuleap\Tracker\Report\Query\Advanced\QueryBuilder\GreaterThanFieldComparisonVisitor;
use Tuleap\Tracker\Report\Query\Advanced\QueryBuilder\GreaterThanOrEqualFieldComparisonVisitor;
use Tuleap\Tracker\Report\Query\Advanced\QueryBuilder\InFieldComparisonVisitor;
use Tuleap\Tracker\Report\Query\Advanced\QueryBuilder\LesserThanFieldComparisonVisitor;
use Tuleap\Tracker\Report\Query\Advanced\QueryBuilder\LesserThanOrEqualFieldComparisonVisitor;
use Tuleap\Tracker\Report\Query\Advanced\QueryBuilder\NotEqualFieldComparisonVisitor;
use Tuleap\Tracker\Report\Query\Advanced\QueryBuilder\NotInFieldComparisonVisitor;
use Tuleap\Tracker\Report\Query\Advanced\QueryBuilder\SearchableVisitor;
use Tuleap\Tracker\Report\Query\Advanced\QueryBuilder\SearchableVisitorParameter;

class QueryBuilderVisitor implements Visitor
{
    /**
     * @var Tracker_FormElementFactory
     */
    private $formelement_factory;
    /**
     * @var NotEqualFieldComparisonVisitor
     */
    private $not_equal_comparison_visitor;
    /**
     * @var EqualFieldComparisonVisitor
     */
    private $equal_comparison_visitor;
    /**
     * @var LesserThanFieldComparisonVisitor
     */
    private $lesser_than_comparison_visitor;
    /**
     * @var GreaterThanFieldComparisonVisitor
     */
    private $greater_than_comparison_visitor;
    /**
     * @var LesserThanOrEqualFieldComparisonVisitor
     */
    private $lesser_than_or_equal_comparison_visitor;
    /**
     * @var GreaterThanOrEqualFieldComparisonVisitor
     */
    private $greater_than_or_equal_comparison_visitor;
    /**
     * @var BetweenFieldComparisonVisitor
     */
    private $between_comparison_visitor;
    /**
     * @var InFieldComparisonVisitor
     */
    private $in_comparison_visitor;

    /**
     * @var NotInFieldComparisonVisitor
     */
    private $not_in_comparison_visitor;
    /**
     * @var SearchableVisitor
     */
    private $searchable_visitor;

    public function __construct(
        Tracker_FormElementFactory $formelement_factory,
        EqualFieldComparisonVisitor $equal_comparison_visitor,
        NotEqualFieldComparisonVisitor $not_equal_comparison_visitor,
        LesserThanFieldComparisonVisitor $lesser_than_comparison_visitor,
        GreaterThanFieldComparisonVisitor $superior_comparison_visitor,
        LesserThanOrEqualFieldComparisonVisitor $lesser_than_or_equal_comparison_visitor,
        GreaterThanOrEqualFieldComparisonVisitor $greater_than_or_equal_comparison_visitor,
        BetweenFieldComparisonVisitor $between_comparison_visitor,
        InFieldComparisonVisitor $in_comparison_visitor,
        NotInFieldComparisonVisitor $not_in_comparison_visitor,
        SearchableVisitor $searchable_visitor
    ) {
        $this->formelement_factory                      = $formelement_factory;
        $this->equal_comparison_visitor                 = $equal_comparison_visitor;
        $this->not_equal_comparison_visitor             = $not_equal_comparison_visitor;
        $this->lesser_than_comparison_visitor           = $lesser_than_comparison_visitor;
        $this->greater_than_comparison_visitor          = $superior_comparison_visitor;
        $this->lesser_than_or_equal_comparison_visitor  = $lesser_than_or_equal_comparison_visitor;
        $this->greater_than_or_equal_comparison_visitor = $greater_than_or_equal_comparison_visitor;
        $this->between_comparison_visitor               = $between_comparison_visitor;
        $this->in_comparison_visitor                    = $in_comparison_visitor;
        $this->not_in_comparison_visitor                = $not_in_comparison_visitor;
        $this->searchable_visitor                       = $searchable_visitor;
    }

    public function buildFromWhere(Visitable $parsed_query, Tracker $tracker)
    {
        return $parsed_query->accept($this, new QueryBuilderParameters($tracker));
    }

    public function visitEqualComparison(EqualComparison $comparison, QueryBuilderParameters $parameters)
    {
        return $comparison->getSearchable()->accept(
            $this->searchable_visitor,
            new SearchableVisitorParameter(
                $comparison,
                $this->equal_comparison_visitor,
                $parameters->getTracker()
            )
        );
    }

    public function visitNotEqualComparison(NotEqualComparison $comparison, QueryBuilderParameters $parameters)
    {
        return $comparison->getSearchable()->accept(
            $this->searchable_visitor,
            new SearchableVisitorParameter(
                $comparison,
                $this->not_equal_comparison_visitor,
                $parameters->getTracker()
            )
        );
    }

    public function visitLesserThanComparison(LesserThanComparison $comparison, QueryBuilderParameters $parameters)
    {
        return $comparison->getSearchable()->accept(
            $this->searchable_visitor,
            new SearchableVisitorParameter(
                $comparison,
                $this->lesser_than_comparison_visitor,
                $parameters->getTracker()
            )
        );
    }

    public function visitGreaterThanComparison(GreaterThanComparison $comparison, QueryBuilderParameters $parameters)
    {
        return $comparison->getSearchable()->accept(
            $this->searchable_visitor,
            new SearchableVisitorParameter(
                $comparison,
                $this->greater_than_comparison_visitor,
                $parameters->getTracker()
            )
        );
    }

    public function visitLesserThanOrEqualComparison(LesserThanOrEqualComparison $comparison, QueryBuilderParameters $parameters)
    {
        return $comparison->getSearchable()->accept(
            $this->searchable_visitor,
            new SearchableVisitorParameter(
                $comparison,
                $this->lesser_than_or_equal_comparison_visitor,
                $parameters->getTracker()
            )
        );
    }

    public function visitGreaterThanOrEqualComparison(GreaterThanOrEqualComparison $comparison, QueryBuilderParameters $parameters)
    {
        return $comparison->getSearchable()->accept(
            $this->searchable_visitor,
            new SearchableVisitorParameter(
                $comparison,
                $this->greater_than_or_equal_comparison_visitor,
                $parameters->getTracker()
            )
        );
    }

    public function visitBetweenComparison(BetweenComparison $comparison, QueryBuilderParameters $parameters)
    {
        return $comparison->getSearchable()->accept(
            $this->searchable_visitor,
            new SearchableVisitorParameter(
                $comparison,
                $this->between_comparison_visitor,
                $parameters->getTracker()
            )
        );
    }

    public function visitInComparison(InComparison $comparison, QueryBuilderParameters $parameters)
    {
        return $comparison->getSearchable()->accept(
            $this->searchable_visitor,
            new SearchableVisitorParameter(
                $comparison,
                $this->in_comparison_visitor,
                $parameters->getTracker()
            )
        );
    }

    public function visitNotInComparison(NotInComparison $comparison, QueryBuilderParameters $parameters)
    {
        return $comparison->getSearchable()->accept(
            $this->searchable_visitor,
            new SearchableVisitorParameter(
                $comparison,
                $this->not_in_comparison_visitor,
                $parameters->getTracker()
            )
        );
    }

    public function visitAndExpression(AndExpression $and_expression, QueryBuilderParameters $parameters)
    {
        $from_where_expression = $and_expression->getExpression()->accept($this, $parameters);

        $tail = $and_expression->getTail();

        return $this->buildAndClause($parameters, $tail, $from_where_expression);
    }

    public function visitOrExpression(OrExpression $or_expression, QueryBuilderParameters $parameters)
    {
        $from_where_expression = $or_expression->getExpression()->accept($this, $parameters);

        $tail = $or_expression->getTail();

        return $this->buildOrClause($parameters, $tail, $from_where_expression);
    }

    public function visitOrOperand(OrOperand $or_operand, QueryBuilderParameters $parameters)
    {
        $from_where_expression = $or_operand->getOperand()->accept($this, $parameters);

        $tail = $or_operand->getTail();

        return $this->buildOrClause($parameters, $tail, $from_where_expression);
    }

    public function visitAndOperand(AndOperand $and_operand, QueryBuilderParameters $parameters)
    {
        $from_where_expression = $and_operand->getOperand()->accept($this, $parameters);

        $tail = $and_operand->getTail();

        return $this->buildAndClause($parameters, $tail, $from_where_expression);
    }

    private function buildAndClause(QueryBuilderParameters $parameters, $tail, $from_where_expression)
    {
        if (! $tail) {
            return $from_where_expression;
        }

        $from_where_tail = $tail->accept($this, $parameters);

        return new FromWhere(
            $from_where_expression->getFrom() . ' ' . $from_where_tail->getFrom(),
            $from_where_expression->getWhere() . ' AND ' . $from_where_tail->getWhere()
        );
    }

    private function buildOrClause(QueryBuilderParameters $parameters, $tail, $from_where_expression)
    {
        if (! $tail) {
            return $from_where_expression;
        }

        $from_where_tail = $tail->accept($this, $parameters);

        return new FromWhere(
            $from_where_expression->getFrom() . ' ' . $from_where_tail->getFrom(),
            '(' . $from_where_expression->getWhere() . ' OR ' . $from_where_tail->getWhere() . ')'
        );
    }
}

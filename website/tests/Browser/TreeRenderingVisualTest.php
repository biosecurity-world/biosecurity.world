<?php

namespace Tests\Browser;

use Tests\TestCase;

class
TreeRenderingVisualTest extends TestCase
{
    public function test_it_can_render_half_bounded_vertices_in_the_first_quadrant()
    {
        $vertices = [];
        for ($i = 0; $i < 8; $i++) {
            $vertices[] = $this->node($i * pi() / 16, ($i + 1) * pi() / 16);
        }
        $this->assertTreeRenderingMatchesSnapshot($vertices);
    }

    public function test_it_can_render_half_bounded_vertices_in_the_second_quadrant()
    {
        $vertices = [];
        for ($i = 0; $i < 8; $i++) {
            $vertices[] = $this->node(pi() / 2 + $i * pi() / 16, pi() / 2 + ($i + 1) * pi() / 16);
        }
        $this->assertTreeRenderingMatchesSnapshot($vertices);
    }

    public function test_it_can_render_half_bounded_vertices_in_the_third_quadrant()
    {
        $vertices = [];
        for ($i = 0; $i < 8; $i++) {
            $vertices[] = $this->node(pi() + $i * pi() / 16, pi() + ($i + 1) * pi() / 16);
        }
        $this->assertTreeRenderingMatchesSnapshot($vertices);
    }

    public function test_it_can_render_half_bounded_vertices_in_the_fourth_quadrant()
    {
        $vertices = [];
        for ($i = 0; $i < 8; $i++) {
            $vertices[] = $this->node(3 * pi() / 2 + $i * pi() / 16, 3 * pi() / 2 + ($i + 1) * pi() / 16);
        }
        $this->assertTreeRenderingMatchesSnapshot($vertices);
    }

    public function test_it_can_render_vertices_touching_the_x_axis()
    {
        $pi = pi();

        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(0, $pi / 3),
            $this->node(2 * $pi / 3, $pi),
            $this->node($pi, 4 * $pi / 3),
            $this->node(5 * $pi / 3, 2 * $pi),
        ]);
    }

    public function test_it_can_render_free_vertices()
    {
        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(0, 5 * pi() / 3),
        ], 'Case: [0, 5pi/3]');

        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(0, pi()),
        ], 'Case: [0, pi]');

        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(pi(), 2 * pi()),
        ], 'Case: [pi, 2pi]');

        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(0, pi() * 2),
        ], 'Case: [0, 2pi]');
    }

    public function test_it_can_render_full_quadrants()
    {
        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(0, pi() / 2),
            $this->node(pi() / 2, pi()),
            $this->node(pi(), 3 * pi() / 2),
            $this->node(3 * pi() / 2, 2 * pi()),
        ]);
    }

    public function test_it_can_render_vertices_with_a_sector_in_two_adjacent_quadrants()
    {
        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(0, 2 * pi() / 3),
        ], 'Case: [0, 2pi/3]');
    }

    public function test_it_can_render_vertices_touching_the_y_axis()
    {
        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(pi() / 2 - pi() / 6, pi() / 2),
            $this->node(pi() / 2, pi() / 2 + pi() / 6),
            $this->node(3 * pi() / 2, 3 * pi() / 2 + pi() / 6),
            $this->node(3 * pi() / 2 - pi() / 6, 3 * pi() / 2),
        ]);
    }

    public function test_it_can_render_side_bounded_vertices()
    {
        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(pi() / 3, 2 * pi() / 3),
            $this->node(2 * pi() / 3, 4 * pi() / 3),
            $this->node(4 * pi() / 3, 5 * pi() / 3),
        ]);
    }

    public function test_it_can_render_node_in_q2_and_q4()
    {
        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(5 * pi() / 6, 10 * pi() / 6),
        ]);
    }
}

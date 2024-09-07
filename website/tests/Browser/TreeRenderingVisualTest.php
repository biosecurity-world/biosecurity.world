<?php

namespace Tests\Browser;

use Tests\TestCase;

class TreeRenderingVisualTest extends TestCase
{
    public function test_it_can_render_0_to_pi_2()
    {
        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(0, pi() / 2),
        ], 'Case: [0, pi/2]');
    }

    public function test_it_can_render_pi_2_to_pi()
    {
        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(pi() / 2, pi()),
        ], 'Case: [pi/2, pi]');
    }

    public function test_it_can_render_to_pi_3pi_2()
    {
        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(pi(), 3 * pi() / 2),
        ], 'Case: [pi, 3pi/2]');
    }

    public function test_it_can_render_to_3pi_2_to_2pi()
    {
        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(3 * pi() / 2, 2 * pi()),
        ], 'Case: [3pi/2, 2pi]');
    }

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

    public function test_it_can_render_vertices_touching_the_x_axis_case_0_pi3()
    {
        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(0, pi() / 3),
        ]);
    }

    public function test_it_can_render_vertices_touching_the_x_axis_case_2pi3_pi()
    {
        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(2 * pi() / 3, pi()),
        ]);
    }

    public function test_it_can_render_vertices_touching_the_x_axis_case_pi_4pi3()
    {
        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(pi(), 4 * pi() / 3),
        ]);
    }

    public function test_it_can_render_vertices_touching_the_x_axis_case_5pi3_2pi()
    {
        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(5 * pi() / 3, 2 * pi()),
        ]);
    }

    public function test_it_can_render_free_vertices_case_0_5pi3()
    {
        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(0, 5 * pi() / 3),
        ]);
    }

    public function test_it_can_render_free_vertices_case_0_pi()
    {
        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(0, pi()),
        ]);
    }

    public function test_it_can_render_free_vertices_case_pi_2pi()
    {
        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(pi(), 2 * pi()),
        ]);
    }

    public function test_it_can_render_free_vertices_case_0_2pi()
    {
        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(0, 2 * pi()),
        ]);
    }

    public function test_it_can_render_vertices_with_a_sector_in_two_adjacent_quadrants_case_0_2pi3()
    {
        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(0, 2 * pi() / 3),
        ]);
    }

    public function test_it_can_render_vertices_with_a_sector_in_two_adjacent_quadrants_case_2pi3_4pi3()
    {
        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(2 * pi() / 3, 4 * pi() / 3),
        ]);
    }

    public function test_it_can_render_vertices_with_a_sector_in_two_adjacent_quadrants_case_4pi3_5pi3()
    {
        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(4 * pi() / 3, 5 * pi() / 3),
        ]);
    }

    public function test_it_can_render_vertices_touching_the_y_axis_case_2pi6_3pi6()
    {
        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(2 * pi() / 6, 3 * pi() / 6),
        ]);
    }

    public function test_it_can_render_vertices_touching_the_y_axis_case_3pi6_4pi6()
    {
        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(3 * pi() / 6, 4 * pi() / 6),
        ]);
    }

    public function test_it_can_render_vertices_touching_the_y_axis_case_8pi6_9pi6()
    {
        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(8 * pi() / 6, 9 * pi() / 6),
        ]);
    }

    public function test_it_can_render_vertices_touching_the_y_axis_case_9pi6_10pi6()
    {
        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(9 * pi() / 6, 10 * pi() / 6),
        ]);
    }

    public function test_it_can_render_node_in_q2_and_q4()
    {
        $this->assertTreeRenderingMatchesSnapshot([
            $this->node(5 * pi() / 6, 10 * pi() / 6),
        ]);
    }
}

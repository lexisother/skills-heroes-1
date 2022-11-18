<?php

namespace App\Controllers;

use App\DTO\Teacher as DTOTeacher;
use App\Lib\ExistingEntryException;
use App\Lib\MissingPropertyException;
use App\Models\Teacher;
use Exception;

class TeacherController extends Controller
{
    /**
     * Fetch a teacher by their name.
     *
     * @throws Exception
     */
    public function fetchTeacher()
    {
        // Get JSON body (regardless of GET or POST (bad practice, I know!))
        $data = json_decode(file_get_contents("php://input"));

        // No body? Fetch all teachers.
        if (empty((array)$data)) {
            echo json_encode(Teacher::all());
        } else {
            // Name provided?
            if (isset($data->first_name)) {
                // Find product(s) with provided name.
                $teachers = Teacher::where('first_name', $data->first_name);

                // Fail if there are no elements. Without this line, the API will send back
                //a status code of 200 with an empty array. This isn't the intended behaviour.
                $teachers->firstOrFail();

                // Send the list back to the client.
                echo json_encode($teachers->get());
            } else if (isset($data->last_name)) {
                $teachers = Teacher::where("last_name", $data->last_name);
                $teachers->firstOrFail();
                echo json_encode($teachers->get());
            } else {
                throw new Exception("Please provide a first or last name!");
            }
        }
    }

    /**
     * Create a teacher with all necessary data.
     *
     * @throws Exception
     */
    public function createTeacher()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty((array)$data)) {
            $vals = ['first_name', 'last_name', 'age', 'class', 'email', 'phone', 'work_days'];
            foreach ($vals as $val) {
                if (isset(((array)$data)[$val]))
                    continue;
                else
                    throw new MissingPropertyException("{$val} is missing from the request body. Are you passing all required values?");
            }

            $teacher = new DTOTeacher(0, $data->first_name, $data->last_name, $data->age, $data->class, $data->email, $data->phone, $data->work_days);

            if (Teacher::where('first_name', $teacher->first_name)->first())
                throw new ExistingEntryException("{$teacher->first_name} {$teacher->last_name} is already present in the database.");

            $res = new Teacher([
                'first_name' => $teacher->first_name,
                'last_name' => $teacher->last_name,
                'age' => $teacher->age,
                'class' => $teacher->class,
                'email' => $teacher->email,
                'phone' => $teacher->phone,
                'work_days' => $teacher->work_days
            ]);
            $res->save();
            echo json_encode($res);
        } else {
            throw new Exception("No data provided!");
        }
    }

    /**
     * Edit a teacher with any provided data.
     *
     * @param $id
     * @return void
     */
    public function editTeacher($id)
    {
        $item = Teacher::findOrFail($id);
        $data = json_decode(file_get_contents("php://input"));
        $item->update((array)$data);
        echo json_encode($item);
    }

    /**
     * Delete a teacher.
     *
     * @param $id
     * @return void
     */
    public function deleteTeacher($id)
    {
        $item = Teacher::findOrFail($id);
        $item->delete();
    }
}

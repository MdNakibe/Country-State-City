@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <span>State</span>
                    <button class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#createModal">Add State</button>
                </div>
                <div class="card-body">
                    <table class="table table-dark" id="statesTable">
                        <thead>
                            <tr>
                                <th scope="col">SL</th>
                                <th scope="col">Name</th>
                                <th scope="col">Country</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($states as $key => $state)
                            <tr id="row-{{ $state->id }}">
                                <td>{{ $key + 1 }}</td>
                                <td id="name-{{ $state->id }}">{{ $state->name }}</td>
                                <td id="country-name-{{ $state->id }}">{{ $state->country->name }}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm" 
                                            onclick="openEditModal({{ $state->id }}, '{{ $state->name }}', {{ $state->country_id }})">Edit</button>
                                    <button class="btn btn-danger btn-sm" 
                                            onclick="deleteState({{ $state->id }})">Delete</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="createForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Add State</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label class="mt-2">State Name</label>
                    <input type="text" class="form-control" name="name" required>

                    <label class="mt-2">Country</label>
                    <select class="form-control" name="country_id" required>
                        <option value="" disabled selected>Select Country</option>
                        @foreach ($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit State</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editId" name="id">
                    <label class="mt-2">State Name</label>
                    <input type="text" id="editName" class="form-control" name="name" required>

                    <label class="mt-2">Country</label>
                    <select class="form-control" id="editCountry" name="country_id" required>
                        @foreach ($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('custom-script')
<script>
    // Create a state
    $('#createForm').submit(function (e) {
        e.preventDefault();
        const formData = $(this).serialize();

        $.post('{{ route("states.store") }}', formData, function (response) {
            alert(response.message);
            $('#statesTable tbody').append(`
                <tr id="row-${response.data.id}">
                    <td>${$('#statesTable tbody tr').length + 1}</td>
                    <td id="name-${response.data.id}">${response.data.name}</td>
                    <td id="country-name-${response.data.id}">${response.data.country.name}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="openEditModal(${response.data.id}, '${response.data.name}', ${response.data.country_id})">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteState(${response.data.id})">Delete</button>
                    </td>
                </tr>
            `);

            // Close modal and reset form
            $('#createModal').modal('hide');
            $('#createForm')[0].reset();
        }).fail(function (error) {
            alert('Error: ' + error.responseJSON.message);
        });
    });

    // Open edit modal
    function openEditModal(id, name, country_id) {
        $('#editId').val(id);
        $('#editName').val(name);
        $('#editCountry').val(country_id);
        $('#editModal').modal('show');
    }

    // Edit a state
    $('#editForm').submit(function (e) {
        e.preventDefault();
        const id = $('#editId').val();
        const formData = $(this).serialize();

        $.ajax({
            url: '{{ route("states.update", ":id") }}'.replace(':id', id),
            type: 'PUT',
            data: formData,
            success: function (response) {
                alert(response.message);
                $(`#name-${id}`).text(response.data.name);
                $(`#country-name-${id}`).text(response.data.country.name);
                $('#editModal').modal('hide');
                $('#editForm')[0].reset();
            },
            error: function (error) {
                alert('Error: ' + error.responseJSON.message);
            }
        });
    });

    // Delete a state
    function deleteState(id) {
        if (confirm('Are you sure?')) {
            $.ajax({
                url: '{{ route("states.destroy", ":id") }}'.replace(':id', id),
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function (response) {
                    alert(response.message);
                    $(`#row-${id}`).remove();
                },
                error: function (error) {
                    alert('Error: ' + error.responseJSON.message);
                }
            });
        }
    }
</script>
@endsection

<?php

namespace Valda\Traits;

trait TransformsResponses
{
    /**
     * Return an ok response.
     *
     * @param  array  $data
     * @return \Illuminate\Http\Response
     */
    protected function okResponse($data = [])
    {
        return $this->response(200, $data);
    }

    /**
     * Return a created response.
     *
     * @param  array  $data
     * @return \Illuminate\Http\Response
     */
    protected function createdResponse($data = [])
    {
        return $this->response(201, $data);
    }

    /**
     * Return a no content response.
     *
     * @param  array  $data
     * @return \Illuminate\Http\Response
     */
    protected function noContentResponse($data = [])
    {
        return $this->response(204, $data);
    }

    /**
     * Return a bad request response.
     *
     * @param  string  $message
     * @param  array  $errors
     * @return \Illuminate\Http\Response
     */
    protected function badRequestResponse($message = 'Bad Request', $errors = [])
    {
        return $this->errorResponse(400, $message, $errors);
    }

    /**
     * Return an unauthorized response.
     *
     * @param  string  $message
     * @param  array  $errors
     * @return \Illuminate\Http\Response
     */
    protected function unauthorizedResponse($message = 'Unauthorized', $errors = [])
    {
        return $this->errorResponse(401, $message, $errors);
    }

    /**
     * Return a not found response.
     *
     * @param  string  $message
     * @param  array  $errors
     * @return \Illuminate\Http\Response
     */
    protected function notFoundResponse($message = 'Not Found', $errors = [])
    {
        return $this->errorResponse(404, $message, $errors);
    }

    /**
     * Return an error response.
     *
     * @param  array  $data
     * @param  string  $message
     * @param  mixed  $errors
     * @return \Illuminate\Http\Response
     */
    protected function errorResponse($code = 422, $message = 'Unprocessable Entity', $errors = [])
    {
        return $this->response($code, [
            'error' => compact('code', 'message', 'errors'),
        ]);
    }

    /**
     * Return a response.
     *
     * @param  integer  $code
     * @param  array  $data
     * @return \Illuminate\Http\Response
     */
    protected function response($code, $data)
    {
        return response()->json($data, $code);
    }
}
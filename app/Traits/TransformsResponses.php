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
     * @return \Illuminate\Http\Response
     */
    protected function badRequestResponse($message = 'Bad Request')
    {
        return $this->errorResponse(400, $message);
    }

    /**
     * Return an unauthorized response.
     *
     * @param  string  $message
     * @return \Illuminate\Http\Response
     */
    protected function unauthorizedResponse($message = 'Unauthorized')
    {
        return $this->errorResponse(401, $message);
    }

    /**
     * Return a not found response.
     *
     * @param  string  $message
     * @return \Illuminate\Http\Response
     */
    protected function notFoundResponse($message = 'Not Found')
    {
        return $this->errorResponse(404, $message);
    }

    /**
     * Return an error response.
     *
     * @param  array  $data
     * @param  string  $message
     * @return \Illuminate\Http\Response
     */
    protected function errorResponse($code = 422, $message = 'Unprocessable Entity')
    {
        return $this->response($code, [
            'error' => compact('code', 'message')
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
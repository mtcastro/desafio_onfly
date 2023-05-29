<?php

namespace App\Http\Controllers;

trait JsonResponses
{
    /**
     * Resposta ERROR, com base no padrão JSend.
     *
     * @param string $message  Mensagem de erro
     * @param int    $code     Código de erro HTTP
     * @param mixed  $debug    Define a informação a ser exibida em modo de DEBUG
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function error($message = '', $code = 500, $debug = null)
    {
        $message = $message ?: __('messages.error');

        $response = [
            'status'  => 'error',
            'message' => $message,
        ];
        
        return response()->json($response, $code);
    }

    /**
     * Resposta SUCCESS, com base no padrão JSend.
     *
     * @param mixed $data Dados da resposta
     * @param string $message Mensagem de sucesso
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function success($data = null, $message = '')
    {
        $response = [
            'status' => 'success',
            'data'   => $data,
        ];

        if ($message) {
            $response['message'] = $message;
        }

        return response()->json($response, 200);
    }

    /**
     * Resposta SUCCESS usando o ID de um JOB como resposta.
     *
     * @param mixed $job
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successWithJob($job)
    {
        $this->dispatch($job);

        return $this->success([ 'job' => $job->getJobStatusId() ]);
    }

    /**
     * Resposta 202 ACCEPTED.
     * @see https://httpstatuses.com/202
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function accepted()
    {
        return response()->json(null, 202);
    }

    /**
     * Resposta FAIL, com base no padrão JSend.
     */
    protected function fail($data = null, $status = 422)
    {
        $response = [
            'status' => 'fail',
            'data'   => $data,
        ];

        return response()->json($response, 422);
    }

    /**
     * 
     */
    protected function validationError($errors)
    {
        foreach ($errors as $k => $error) {
            if(!is_array($error)) {
                $errors[$k] = [$error];
            }
        }

        return response()->json($errors, 422);
    }

    /**
     * Resposta de erro com código 403.
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function forbidden($message = 'Não autorizado.')
    {
        return $this->error($message, 403);
    }
}
